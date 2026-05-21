<h2>User List</h2>

<table border="1">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
</tr>

<?php foreach($users as $u): ?>
<tr>
    <td><?= $u->id ?></td>
    <td><?= $u->firstname . ' ' . $u->lastname ?></td>
    <td><?= $u->email ?></td>
</tr>
<?php endforeach; ?>

</table>