
<?php
include "dbconfig.php"; // 包含数据库配置文件
// 查询所有类别
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if (isset($_POST['create'])) {
    // 获取表单数据
    $catid = $_POST['catid'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $imagePath = null; // 初始化图片路径变量

    // 如果有新图片上传，处理图片上传逻辑
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/gif', 'image/png'];
        $allowedSize = 10 * 1024 * 1024; // 10 MB
        $imageType = $_FILES['image']['type'];
        $imageSize = $_FILES['image']['size'];

        if (in_array($imageType, $allowedTypes) && $imageSize <= $allowedSize) {
            $tempPath = $_FILES['image']['tmp_name'];
            $imagePath = 'uploads/' . basename($_FILES['image']['name']);
            // 获取原始图像尺寸
            list($originalWidth, $originalHeight) = getimagesize($tempPath);
            // 创建一个新的800x800图像
            $newWidth = 800;
            $newHeight = 800;
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            // 根据文件类型加载图片
            $sourceImage = false;
            if (file_exists($tempPath) && is_readable($tempPath)) {
                switch ($imageType) {
                    case 'image/jpeg':
                        $sourceImage = imagecreatefromjpeg($tempPath);
                        break;
                    case 'image/gif':
                        $sourceImage = imagecreatefromgif($tempPath);
                        break;
                    case 'image/png':
                        $sourceImage = imagecreatefrompng($tempPath);
                        break;
                }
                if (!$sourceImage) {
                    die("无法创建图像资源。文件类型可能不支持或文件损坏。");
                }
            } else {
                die("临时文件不存在或无法读取。");
            }
            // 调整图像大小并重新采样
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            // 保存调整大小的图像
            switch ($imageType) {
                case 'image/jpeg':
                    imagejpeg($newImage, $imagePath, 90); // 第三个参数是品质
                    break;
                case 'image/gif':
                    imagegif($newImage, $imagePath);
                    break;
                case 'image/png':
                    imagepng($newImage, $imagePath, 9); // 第三个参数是压缩级别
                    break;
            }
            // 清理内存
            imagedestroy($sourceImage);
            imagedestroy($newImage);

        } else {
            die('不支持的文件类型或文件过大。');
        }
    } else {
        $imagePath = ''; // 如果没有上传图片，设置为空字符串
    }

    // 构建SQL插入语句
    $sql = "INSERT INTO `products` (`catid`, `name`, `price`, `description`, `image`) VALUES ('$catid', '$name', '$price', '$description', '$imagePath')";

    // 执行查询
    $result = $conn->query($sql);

    // 处理查询结果
    if ($result == TRUE) {
        echo "新产品创建成功。";
        header('Location: AdminPanel.php'); // 成功后重定向到产品查看页面
    } else {
        echo "错误：" . $sql . "<br>" . $conn->error; // 失败输出错误信息
    }
}
?>

<!-- HTML 表单部分 -->
<h2>创建新产品</h2>
<form action="" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>产品信息:</legend>
        名称:<br>
        <input type="text" name="name">
        <br>
        类别:<br>
        <select name="catid">
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['catid']; ?>">
                    <?php echo $category['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        价格:<br>
        <input type="text" name="price">
        <br>
        描述:<br>
        <textarea name="description"></textarea>
        <br>
        图片（可选）:<br>
        <input type="file" name="image">
        <br><br>
        <input type="submit" value="创建" name="create">
    </fieldset>
</form>