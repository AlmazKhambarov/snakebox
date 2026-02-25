const fs = require("fs");
const express = require("express");
const http = require("http");
const https = require("https");
const { createClient } = require("redis");
const { Server } = require("socket.io");
const config = require("./config");
const axios = require("axios");

const getProtocolOptions = () => {
    if (config.port === 8443) {
        return {
            protocol: "https",
            protocolOptions: {
                key: fs.readFileSync(config.ssl.key),
                cert: fs.readFileSync(config.ssl.cert),
            },
        };
    }
    return { protocol: "http" };
};

const options = getProtocolOptions();
const app = express();

const server =
    options.protocol === "https"
        ? https.createServer(options.protocolOptions, app)
        : http.createServer(app);

const io = new Server(server, {
    allowEIO3: true,
    cors: {
        origin: true,
        credentials: true,
    },
});

global.io = io;

const client = createClient({
    socket: {
        host: "127.0.0.1", // IPv4 localhost
        port: 6379,
    },
});
const subscriber = client.duplicate();
subscriber.connect();

const channels = ["userBalance", "liveFeed", "setItemsStatus", "newRaffle", "giveawayFinished"];

subscriber.subscribe(channels, (message, channel) => {
    message = JSON.parse(message);
    io.sockets.emit(channel, message);
});

const checkItems = async () => {
    console.log("checkItems");
    try {
        await axios
            .post(`${config.domain}/api/market/checkItems`)
            .then(() => {})
            .catch((error) => {
                console.log(error);
            });
    } catch (e) {
        console.log(e);
    }
};

setInterval(async () => {
    await checkItems();
}, 100000);

const ipsConnected = new Map();
let realOnline = 0;
let displayOnline = 0;
let isOnlineInitialized = false;


// Плавное изменение онлайн каждые 10-20 секунд
setInterval(() => {
    const change = Math.floor(Math.random() * 8) - 4; // -4 to +4
    if (change !== 0) {
        displayOnline += change;
        // Не даем упасть ниже реального онлайн + минимум фейка
        const minOnline = realOnline + 20;
        if (displayOnline < minOnline) {
            displayOnline = minOnline;
        }
        // Не даем подняться слишком высоко
        const maxOnline = realOnline + 50;
        if (displayOnline > maxOnline) {
            displayOnline = maxOnline;
        }
        io.emit("online", displayOnline);
    }
}, Math.floor(Math.random() * 11 + 10) * 1000); // 10-20 секунд

io.on("connection", (socket) => {
    const address = socket.handshake.address;

    if (!ipsConnected.has(address)) {
        ipsConnected.set(address, new Set());
    }

    ipsConnected.get(address).add(socket.id);
    realOnline = ipsConnected.size;

    // При первом подключении инициализируем онлайн
    if (!isOnlineInitialized) {
        const fakeOnline = Math.floor(Math.random() * 31) + 20; // 20-50
        displayOnline = realOnline + fakeOnline;
        isOnlineInitialized = true;
    }

    // Отправляем текущий онлайн новому пользователю
    socket.emit("online", displayOnline);

    socket.on("disconnect", () => {
        if (ipsConnected.has(address)) {
            ipsConnected.get(address).delete(socket.id);
            if (ipsConnected.get(address).size === 0) {
                ipsConnected.delete(address);
            }
            realOnline = ipsConnected.size;
            // Не обновляем displayOnline сразу, пусть плавно меняется
        }
    });
});

server.listen(config.port, () => {
    console.log(`Сервер запущен: ${config.domain}:${config.port}`);
});
