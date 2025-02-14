<!-- FOOTER THINGS -->

<?php
    if (isset($page_scripts)) {
        foreach ($page_scripts as $script) {
            echo '<script src="./scripts/' . $script . '"></script>';
        }
    }
?>
</body>
</html>