export default function validatePassword(password, confirmationPassword )
{
    if (password.length < 6) {
        throw new Error('Password must contain 6 or more characters');
    } else if (password.length > 50) {
        throw new Error('Password must contain less than 50 characters');
    } else if (!password.match(/^[a-zA-Z0-9!~_&*%@$]+$/)) {
        throw new Error('Password must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
    } else if (!password.match(/\d/)) {
        throw new Error('Password must contain at least one number');
    } else if (!password.match(/[!~_&*%@$]/)) {
        throw new Error('Password must contain at least one of this symbols "!", "~", "_", "&", "*", "%", "@", "$"');
    } else if (password !== confirmationPassword) {
        throw new Error('Password and confirmation password must match');
    } else {
        return true;
    }
}