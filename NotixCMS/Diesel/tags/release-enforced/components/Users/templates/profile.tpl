{$user=$model.user}
<table>
    <tr>
        <td style="width:150px;"><input type='hidden' value='{$user->id}'/>Логин</td>
        <td>{$user->login}</td>
    </tr>
    <tr>
        <td>Пароль</td>
        <td style='display:none;'>{$user->passwd}</td>
    </tr>
    <tr>
        <td>Имя</td>
        <td>{$user->firstName}</td>
    </tr>
    <tr>
        <td>Фамилия</td>
        <td>{$user->lastName}</td>
    </tr>
    <tr>
        <td>E-mail</td>
        <td>{$user->email}</td>
    </tr>
    <tr>
        <td>Телефон</td>
        <td>{$user->phone}</td>
    </tr>
    <tr>
        <td>Адрес</td>
        <td>{$user->address}</td>
    </tr>
</table>