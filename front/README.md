# frontend

## Сборка CSS

Проект использует автоматизированную систему объединения CSS файлов для оптимизации производительности.

### Как работает объединение CSS

1. **Исходные файлы**: CSS файлы находятся в `public/css/`
   - `styles.min.css` - основные стили
   - `toastr.min.css` - уведомления
   - `tippy.css` - подсказки
   - `fancybox.css` - модальные окна
   - `nouislider.css` - слайдеры

2. **Объединение**: При сборке запускается скрипт `build-css.js`, который:
   - Читает все CSS файлы
   - Объединяет их в один файл `public/css/combined.min.css`
   - Обновляет `index.html` для использования объединенного файла

3. **Команды**:
   ```bash
   # Объединить CSS файлы
   npm run build:css

   # Сборка с автоматическим объединением CSS
   npm run build

   # Запуск dev сервера (CSS объединяется автоматически)
   npm run dev
   ```

### Преимущества

- ✅ **Уменьшение количества HTTP запросов** - с 5 запросов до 1
- ✅ **Улучшение производительности** - быстрее загрузка страницы
- ✅ **Автоматизация** - объединение происходит автоматически при сборке
- ✅ **Кеширование** - браузер кеширует один большой файл вместо 5 маленьких

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

```sh
npm run build
```
