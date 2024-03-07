export default function validateNickname(nickname)
{
    if (nickname.length < 2) {
        throw new Error('Nickname must contain 2 or more characters');
    } else if (nickname.length > 20) {
        throw new Error('Nickname must contain less than 20 characters');
    } else if (!nickname.match(/^[a-zA-Z0-9!~_&*%@$]+$/)) {
        throw new Error('Nickname must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
    } else {
        return true;
    }
}