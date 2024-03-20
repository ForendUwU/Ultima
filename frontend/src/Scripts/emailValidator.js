export default function validateEmail(email)
{
    if (!email) {
        throw new Error('Email is empty');
    } else if (email.length < 2) {
        throw new Error('Email must contain 2 or more characters');
    } else if (email.length > 50) {
        throw new Error('Email must contain less than 20 characters');
    } else if (!email.match(/\S+@\S+\.\S+/)) {
        throw new Error('Incorrect email format');
    } else {
        return true;
    }
}