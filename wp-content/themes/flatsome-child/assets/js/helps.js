const isJsonString = (str) => {
    try {
        JSON.stringify(str);
        return true;
    } catch (e) {
        return false;
    }
}