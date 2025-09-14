<?php
session_start();
include("../../Connection/db.php");

// Check session & role
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/login.php");
    exit();
}

$message = "";

// --- Handle Users CRUD ---
if (isset($_POST['action_user'])) {
    $action = $_POST['action_user'];
    $id = $_POST['user_id'] ?? null;

    // Escape inputs
    $fullname   = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
    $username   = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $email      = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone      = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $address    = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $membership = mysqli_real_escape_string($conn, $_POST['membership'] ?? 'None');
    $password   = $_POST['password'] ?? '';

    if ($password) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $password_sql = ", password='" . mysqli_real_escape_string($conn, $password_hashed) . "'";
    } else {
        $password_sql = "";
    }

    if ($action === "add") {
        // Check duplicate email
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Error: This email is already registered!";
        } else {
            $sql = "INSERT INTO users (fullname, username, email, phone, address, membership, password)
                    VALUES ('$fullname','$username','$email','$phone','$address','$membership','$password_hashed')";
            $message = mysqli_query($conn, $sql) ? "User added successfully!" : "Error: " . mysqli_error($conn);
        }
    } elseif ($action === "update" && $id) {
        // Prevent duplicate email while updating
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id!='$id'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Error: This email is already used by another user!";
        } else {
            $sql = "UPDATE users SET fullname='$fullname', username='$username', email='$email', phone='$phone', 
                    address='$address', membership='$membership' $password_sql WHERE id='$id'";
            $message = mysqli_query($conn, $sql) ? "User updated successfully!" : "Error: " . mysqli_error($conn);
        }
    } elseif ($action === "delete" && $id) {
        $sql = "DELETE FROM users WHERE id='$id'";
        $message = mysqli_query($conn, $sql) ? "User deleted successfully!" : "Error: " . mysqli_error($conn);
    }
}

// --- Handle Vendors CRUD ---
if (isset($_POST['action_vendor'])) {
    $action = $_POST['action_vendor'];
    $id = $_POST['vendor_id'] ?? null;

    // Escape inputs
    $fullname   = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
    $username   = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $email      = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone      = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $address    = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $experience = mysqli_real_escape_string($conn, $_POST['experience'] ?? '');
    $password   = $_POST['password'] ?? '';

    if ($password) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $password_sql = ", password='" . mysqli_real_escape_string($conn, $password_hashed) . "'";
    } else {
        $password_sql = "";
    }

    if ($action === "add") {
        $sql = "INSERT INTO vendors (fullname, username, email, phone, address, experience, password)
                VALUES ('$fullname','$username','$email','$phone','$address','$experience','$password_hashed')";
        $message = mysqli_query($conn, $sql) ? "Vendor added successfully!" : "Error: " . mysqli_error($conn);
    } elseif ($action === "update" && $id) {
        $sql = "UPDATE vendors SET fullname='$fullname', username='$username', email='$email', phone='$phone', 
                address='$address', experience='$experience' $password_sql WHERE id='$id'";
        $message = mysqli_query($conn, $sql) ? "Vendor updated successfully!" : "Error: " . mysqli_error($conn);
    } elseif ($action === "delete" && $id) {
        $sql = "DELETE FROM vendors WHERE id='$id'";
        $message = mysqli_query($conn, $sql) ? "Vendor deleted successfully!" : "Error: " . mysqli_error($conn);
    }
}

// --- Handle edit pre-fill ---
$editUser = null;
if (isset($_POST['edit_user'])) {
    $editUser = [
        'id' => $_POST['user_id'],
        'fullname' => $_POST['fullname'],
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'membership' => $_POST['membership']
    ];
}

$editVendor = null;
if (isset($_POST['edit_vendor'])) {
    $editVendor = [
        'id' => $_POST['vendor_id'],
        'fullname' => $_POST['fullname'],
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'experience' => $_POST['experience']
    ];
}

// Fetch users & vendors
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
$vendors = mysqli_query($conn, "SELECT * FROM vendors ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users & Vendors</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/manage.css">
</head>
<body>

<?php include("navbar.php"); ?>
<div class="manage-container">
    <h1>Manage Users & Vendors</h1>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>

    <!-- USERS SECTION -->
    <section class="table-section">
        <h2>Users</h2>

        <!-- Add/Update User Form -->
        <h3><?= $editUser ? "Update User" : "Add New User" ?></h3>
        <form method="POST" class="crud-form">
            <?php if ($editUser): ?>
                <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
                <input type="hidden" name="action_user" value="update">
            <?php else: ?>
                <input type="hidden" name="action_user" value="add">
            <?php endif; ?>

            <input type="text" name="fullname" placeholder="Full Name" value="<?= $editUser['fullname'] ?? '' ?>" required>
            <input type="text" name="username" placeholder="Username" value="<?= $editUser['username'] ?? '' ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= $editUser['email'] ?? '' ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?= $editUser['phone'] ?? '' ?>">
            <input type="text" name="address" placeholder="Address" value="<?= $editUser['address'] ?? '' ?>">
            <input type="text" name="membership" placeholder="Membership" value="<?= $editUser['membership'] ?? 'free' ?>" required>
            <input type="password" name="password" placeholder="Password (leave blank to keep old)">
            <button type="submit"><?= $editUser ? "Update User" : "Add User" ?></button><br>
        </form>
        <br>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>Full Name</th><th>Username</th><th>Email</th><th>Phone</th>
                <th>Address</th><th>Membership</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['fullname']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['phone']) ?></td>
                    <td><?= htmlspecialchars($u['address']) ?></td>
                    <td><?= htmlspecialchars($u['membership']) ?></td>
                    <td>
                        <!-- Delete -->
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" name="action_user" value="delete" onclick="return confirm('Delete this user?')">Delete</button>
                        </form>
                        <!-- Update -->
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="fullname" value="<?= htmlspecialchars($u['fullname']) ?>">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($u['username']) ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($u['email']) ?>">
                            <input type="hidden" name="phone" value="<?= htmlspecialchars($u['phone']) ?>">
                            <input type="hidden" name="address" value="<?= htmlspecialchars($u['address']) ?>">
                            <input type="hidden" name="membership" value="<?= htmlspecialchars($u['membership']) ?>">
                            <button type="submit" name="edit_user" value="1">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        
    </section>

    <!-- VENDORS SECTION -->
    <section class="table-section">
        <br><br><hr><hr><hr><br>
        <h2>Vendors</h2>

        <!-- Add/Update Vendor Form -->
        <h3><?= $editVendor ? "Update Vendor" : "Add New Vendor" ?></h3>
        <form method="POST" class="crud-form">
            <?php if ($editVendor): ?>
                <input type="hidden" name="vendor_id" value="<?= $editVendor['id'] ?>">
                <input type="hidden" name="action_vendor" value="update">
            <?php else: ?>
                <input type="hidden" name="action_vendor" value="add">
            <?php endif; ?>

            <input type="text" name="fullname" placeholder="Full Name" value="<?= $editVendor['fullname'] ?? '' ?>" required>
            <input type="text" name="username" placeholder="Username" value="<?= $editVendor['username'] ?? '' ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= $editVendor['email'] ?? '' ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?= $editVendor['phone'] ?? '' ?>">
            <input type="text" name="address" placeholder="Address" value="<?= $editVendor['address'] ?? '' ?>">
            <input type="text" name="experience" placeholder="Experience" value="<?= $editVendor['experience'] ?? '' ?>">
            <input type="password" name="password" placeholder="Password (leave blank to keep old)">
            <button type="submit"><?= $editVendor ? "Update Vendor" : "Add Vendor" ?></button>
        </form>
        <br>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>Full Name</th><th>Username</th><th>Email</th><th>Phone</th>
                <th>Address</th><th>Experience</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($v = mysqli_fetch_assoc($vendors)): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= htmlspecialchars($v['fullname']) ?></td>
                    <td><?= htmlspecialchars($v['username']) ?></td>
                    <td><?= htmlspecialchars($v['email']) ?></td>
                    <td><?= htmlspecialchars($v['phone']) ?></td>
                    <td><?= htmlspecialchars($v['address']) ?></td>
                    <td><?= htmlspecialchars($v['experience']) ?></td>
                    <td>
                        <!-- Delete -->
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="vendor_id" value="<?= $v['id'] ?>">
                            <button type="submit" name="action_vendor" value="delete" onclick="return confirm('Delete this vendor?')">Delete</button>
                        </form>
                        <!-- Update -->
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="vendor_id" value="<?= $v['id'] ?>">
                            <input type="hidden" name="fullname" value="<?= htmlspecialchars($v['fullname']) ?>">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($v['username']) ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($v['email']) ?>">
                            <input type="hidden" name="phone" value="<?= htmlspecialchars($v['phone']) ?>">
                            <input type="hidden" name="address" value="<?= htmlspecialchars($v['address']) ?>">
                            <input type="hidden" name="experience" value="<?= htmlspecialchars($v['experience']) ?>">
                            <button type="submit" name="edit_vendor" value="1">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        
    </section>
</div>
</body>
</html>
