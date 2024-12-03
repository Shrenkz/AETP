<?php
$host = 'localhost';
$db = 'AETP';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hero_title = $_POST['hero_title'];
    $hero_description = $_POST['hero_description'];
    $about1_title = $_POST['about1_title'];
    $about1_description = $_POST['about1_description'];
    $about2_title = $_POST['about2_title'];
    $about2_description = $_POST['about2_description'];

    $target_dir = "uploads/";
    $about1_image = $_POST['existing_about1_image'];

    if (!empty($_FILES['about1_image']['name'])) {
        $filename = basename($_FILES['about1_image']['name']);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['about1_image']['tmp_name'], $target_file)) {
            $about1_image = $target_file;
        } else {
            echo "Error uploading file.";
        }
    }

    $sql = "UPDATE page_content 
            SET hero_title=?, hero_description=?, about1_title=?, about1_description=?, about1_image=?, about2_title=?, about2_description=? 
            WHERE id=1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $hero_title, $hero_description, $about1_title, $about1_description, $about1_image, $about2_title, $about2_description);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit();
}

$result = $conn->query("SELECT * FROM page_content WHERE id=1");
$content = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="./css/admin.css">
    <link rel="icon" type="image/svg+xml" href="../svg/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <div class="menu">
            <a href="admin.php" class="active">
                <i class="fas fa-home"></i> Landing Page Management
            </a>
            <a href="3d_management.php">
                <i class="fas fa-cogs"></i> 3D Management
            </a>
        </div>
        <div class="mode-toggle">
            <input type="checkbox" id="darkModeToggle" />
            <label for="darkModeToggle" class="switch"></label>
            <label for="darkModeToggle" class="mode-label">Dark Mode</label>
        </div>
        <div class="logout">
            <a href="../actions/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="content">
        <div class="form-container">
            <h1>Landing Page Management</h1>
            <form method="POST" enctype="multipart/form-data" id="contentForm">
                <section>
                    <h2>Hero Section</h2>
                    <div class="form-group">
                        <label>Hero Title:</label>
                        <input type="text" name="hero_title" value="<?php echo htmlspecialchars($content['hero_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Hero Description:</label>
                        <textarea name="hero_description" required><?php echo htmlspecialchars($content['hero_description']); ?></textarea>
                    </div>
                </section>

                <section>
                    <h2>About Section - Container 1</h2>
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="about1_title" value="<?php echo htmlspecialchars($content['about1_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="about1_description" required><?php echo htmlspecialchars($content['about1_description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image:</label>
                        <input type="file" name="about1_image" accept="image/*">
                        <input type="hidden" name="existing_about1_image" value="<?php echo htmlspecialchars($content['about1_image']); ?>">
                        <?php if (!empty($content['about1_image'])): ?>
                            <img src="./<?php echo htmlspecialchars($content['about1_image']); ?>" alt="Current Image" class="preview-image">
                        <?php endif; ?>
                    </div>
                </section>

                <section>
                    <h2>About Section - Container 2</h2>
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="about2_title" value="<?php echo htmlspecialchars($content['about2_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="about2_description" required><?php echo htmlspecialchars($content['about2_description']); ?></textarea>
                    </div>
                </section>

                <button type="submit" class="btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to save changes?</h3>
            <button class="btn" id="confirmSaveBtn">Yes</button>
            <button class="btn" id="cancelSaveBtn">Cancel</button>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h3>Changes saved successfully!</h3>
            <button class="btn" id="closeSuccessModal">Close</button>
        </div>
    </div>

    <script>
        const contentForm = document.getElementById('contentForm');
        const confirmationModal = document.getElementById('confirmationModal');
        const successModal = document.getElementById('successModal');
        const confirmSaveBtn = document.getElementById('confirmSaveBtn');
        const cancelSaveBtn = document.getElementById('cancelSaveBtn');
        const closeSuccessModal = document.getElementById('closeSuccessModal');

        contentForm.addEventListener('submit', function(event) {
            event.preventDefault();
            confirmationModal.style.display = 'flex';
        });

        confirmSaveBtn.addEventListener('click', function() {
            contentForm.submit();
            confirmationModal.style.display = 'none';
            successModal.style.display = 'flex';
        });

        // Handle cancel save
        cancelSaveBtn.addEventListener('click', function() {
            confirmationModal.style.display = 'none';
        });

        // Close success modal
        closeSuccessModal.addEventListener('click', function() {
            successModal.style.display = 'none';
        });

        // Ensure modals are hidden by default
        window.addEventListener('click', function(event) {
            if (event.target === confirmationModal || event.target === successModal) {
                confirmationModal.style.display = 'none';
                successModal.style.display = 'none';
            }
        });

        // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            document.querySelector('.sidebar').classList.add('dark-mode');
            document.querySelector('.content').classList.add('dark-mode');
            document.querySelector('.form-container').classList.add('dark-mode');
            darkModeToggle.checked = true;
        }

        darkModeToggle.addEventListener('change', function() {
            if (darkModeToggle.checked) {
                document.body.classList.add('dark-mode');
                document.querySelector('.sidebar').classList.add('dark-mode');
                document.querySelector('.content').classList.add('dark-mode');
                document.querySelector('.form-container').classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                document.body.classList.remove('dark-mode');
                document.querySelector('.sidebar').classList.remove('dark-mode');
                document.querySelector('.content').classList.remove('dark-mode');
                document.querySelector('.form-container').classList.remove('dark-mode');
                localStorage.removeItem('darkMode');
            }
        });
    </script>
</body>

</html>