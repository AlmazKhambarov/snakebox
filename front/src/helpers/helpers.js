function getItemRarityClass(rarity = "") {
    // Если rarity нет или пустая строка — сразу возвращаем consumer
    if (!rarity) {
        return "consumer";
    }

    rarity = rarity.toLowerCase();
    let style = "consumer";

    if (rarity.includes("industrial")) {
        style = "industrial";
    } else if (rarity.includes("mil-spec")) {
        style = "milspec";
    } else if (rarity.includes("restricted")) {
        style = "restricted";
    } else if (rarity.includes("classified")) {
        style = "classified";
    } else if (rarity.includes("covert")) {
        style = "covert";
    } else if (rarity.includes("★")) {
        style = "rare";
    } else if (rarity.includes("consumer")) {
        style = "consumer";
    }

    // Особое условие для ножей
    if (rarity.includes("knife") && rarity.includes("covert")) {
        style = "rare";
    }

    return style;
}


export { getItemRarityClass };
