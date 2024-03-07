export default function validateLogin(login)
{
    if (login.length < 6) {
        throw new Error('Login must contain 6 or more characters');
    } else if (login.length > 20) {
        throw new Error('Login must contain less than 20 characters');
    } else if (!login.match(/^[a-zA-Z0-9!~_&*%@$]+$/)) {
        throw new Error('Login must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
    } else {
        return true;
    }
}