import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Список CSS файлов для объединения
const cssFiles = [
    'public/css/styles.min.css',
    'public/css/toastr.min.css',
    'public/css/tippy.css',
    'public/css/fancybox.css',
    'public/css/nouislider.css'
];

const outputFile = 'public/css/combined.min.css';

// Функция для объединения CSS файлов
function combineCSS() {
    let combinedCSS = '/* Объединенный CSS файл - создан автоматически */\n\n';

    cssFiles.forEach(file => {
        try {
            const filePath = path.join(__dirname, file);
            if (fs.existsSync(filePath)) {
                const content = fs.readFileSync(filePath, 'utf8');
                combinedCSS += `/* ${file} */\n${content}\n\n`;
                console.log(`✓ Добавлен: ${file}`);
            } else {
                console.warn(`⚠ Файл не найден: ${file}`);
            }
        } catch (error) {
            console.error(`✗ Ошибка чтения ${file}:`, error.message);
        }
    });

    // Записываем объединенный файл
    try {
        fs.writeFileSync(path.join(__dirname, outputFile), combinedCSS);
        console.log(`\n🎉 CSS файлы успешно объединены в ${outputFile}`);
        console.log(`📊 Размер файла: ${(combinedCSS.length / 1024).toFixed(2)} KB`);
    } catch (error) {
        console.error('✗ Ошибка записи файла:', error.message);
    }
}

// Функция для обновления index.html
function updateHTML() {
    const htmlFile = 'index.html';
    const htmlPath = path.join(__dirname, htmlFile);

    try {
        let htmlContent = fs.readFileSync(htmlPath, 'utf8');

        // Заменяем несколько подключений CSS на одно
        const oldCSSLinks = `    <link rel="stylesheet" href="/css/styles.min.css" />
    <link rel="stylesheet" href="/css/toastr.min.css" />
    <link rel="stylesheet" href="/css/tippy.css" />
    <link rel="stylesheet" href="/css/fancybox.css" />


    <link rel="stylesheet" href="/fonts/Roboto/stylesheet.css" />
    <link rel="stylesheet" href="/css/nouislider.css" />`;

        const newCSSLink = `    <link rel="stylesheet" href="/css/combined.min.css" />
    <link rel="stylesheet" href="/fonts/Roboto/stylesheet.css" />`;

        if (htmlContent.includes(oldCSSLinks)) {
            htmlContent = htmlContent.replace(oldCSSLinks, newCSSLink);
            fs.writeFileSync(htmlPath, htmlContent);
            console.log('✓ index.html обновлен для использования объединенного CSS');
        } else {
            console.log('ℹ index.html уже содержит объединенный CSS или имеет другую структуру');
        }
    } catch (error) {
        console.error('✗ Ошибка обновления index.html:', error.message);
    }
}

// Запускаем объединение
console.log('🔄 Объединение CSS файлов...');
combineCSS();
updateHTML();
