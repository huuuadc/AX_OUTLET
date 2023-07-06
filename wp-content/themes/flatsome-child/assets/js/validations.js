const validateEmail = (email) => {
    if (!email || !email.length) return true
    const re = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return re.test(email)
};

const validatePhone = (v) => {
    if (!v || !v.length) return false
    const phone1 = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
    const phone2 = /^\(?([0-9]{4})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
    return v.match(phone1) || v.match(phone2)
};