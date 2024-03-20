export default function validateName(name, isChanging = false)
{
    if (isChanging) {
        if (!name) {
            return true;
        } else if (name.length < 2) {
            throw new Error('Name must contain 2 or more characters');
        } else if (name.length > 30) {
            throw new Error('Name must contain less than 30 characters');
        } else if (!name.match(/^[a-zA-Z]+$/)) {
            throw new Error('Nickname must contain only letters');
        } else {
            return true;
        }
    } else {
        if (name.length < 2) {
            throw new Error('Name must contain 2 or more characters');
        } else if (name.length > 30) {
            throw new Error('Name must contain less than 30 characters');
        } else if (!name.match(/^[a-zA-Z]+$/)) {
            throw new Error('Nickname must contain only letters');
        } else {
            return true;
        }
    }
}