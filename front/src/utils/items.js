function getItemRarityClass(rarity = "") {
    let style = "consumer";
    rarity = rarity.toLowerCase();

    if (rarity.indexOf("industrial") > -1) {
        style = "industrial";
    } else if (rarity.indexOf("mil-spec") > -1) {
        style = "milspec";
    } else if (rarity.indexOf("restricted") > -1) {
        style = "restricted";
    } else if (rarity.indexOf("classified") > -1) {
        style = "classified";
    } else if (rarity.indexOf("covert") > -1) {
        style = "covert";
    } else if (rarity.indexOf("★") > -1) {
        style = "rare";
    } else if (rarity.indexOf("consumer") > -1) {
        style = "consumer";
    }

    if (rarity.indexOf("knife") > -1 && rarity.indexOf("covert") > -1) {
        style = "rare";
    }

    return style;
}

const getItemImageUrl = (icon_url, resolution) => {
    return `https://community.cloudflare.steamstatic.com/economy/image/${icon_url}/${resolution}`;
};

const getNameParts = (name) => {
    return (name ?? "")
        .split("|")
        .map((n) => n.replace(/(^[^\w\d()]|[^\w\d()]$)/gi, ""));
};

const getItemType = (name) => {
    const nameParts = getNameParts(name);
    if (nameParts.length === 1) return null;
    return nameParts[0].replace(/StatTrak.*?\s/i, "[ST] ");
};

const getItemName = (name) => {
    const nameParts = getNameParts(name);
    if (nameParts.length === 1) return nameParts[0];
    return nameParts[1].replace(/\(.+\)$/, "");
};

const shuffleArray = (array) => {
    let currentIndex = array.length,
        randomIndex;

    // While there remain elements to shuffle.
    while (currentIndex > 0) {
        // Pick a remaining element.
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex--;

        // And swap it with the current element.
        [array[currentIndex], array[randomIndex]] = [
            array[randomIndex],
            array[currentIndex],
        ];
    }

    return array;
};

export {
    getItemRarityClass,
    getItemImageUrl,
    getItemType,
    getItemName,
    shuffleArray,
};
