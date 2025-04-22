<?php
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'domain' => $_SERVER['HTTP_HOST'],
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();

$page_title = "About Us";
$page_styles = ["about-us.css"];
include "./views/header.php";
?>
<!-- Hero Section -->
<div class="hero" style="background: url('assets/images/hero_about.jpg') no-repeat center center; background-size: cover; height: 200px; position: relative; color: #fff;">
  <div class="hero-overlay" style="background: rgba(0,0,0,0.6); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
  <div class="hero-content" style="position: relative; z-index: 2; padding: 60px 20px; text-align: center;">
    <h1>About SmartSummaries</h1>
  </div>
</div>

<!-- Main Content -->
<div class="about-container">

  <!-- Project Inspiration -->
  <h2>Project Inspiration &amp; Background</h2>
  <p>
    SmartSummaries began as a creative challenge during our semester-long project. As students, we noticed how overwhelming it can be to sift through endless online articles, yet many of these sources contain valuable insights. Inspired by the rise of social media and content curation tools, our team set out to build a platform that transforms detailed web content into concise, engaging posts. This project reflects our desire to make information more accessible and shareable.
  </p>

  <!-- Objectives & Goals -->
  <h2>Objectives &amp; Goals</h2>
  <p>
    Our primary objective with SmartSummaries is to streamline the process of content sharing. We aim to empower users to quickly convert in-depth articles into summaries perfect for social media platforms like Facebook and Twitter. Through this project, we are also focused on expanding our technical skills—exploring web scraping, natural language processing, and responsive design. Ultimately, our goal is to create a functional tool that meets real user needs.
  </p>

  <!-- Teams & Roles Section -->
  <h2>Teams &amp; Roles</h2>
  <p>
    <!-- This section is intentionally left blank for now -->
  </p>

  <!-- Technologies & Methodologies -->
  <h2>Technologies &amp; Methodologies</h2>
  <p>
    To build SmartSummaries, we utilized a modern tech stack that includes <strong>PHP</strong> to manage server-side logic and dynamically fetch and process web content. <strong>Python</strong> was used in developing our summarization algorithms, efficiently handling data processing and natural language tasks. The website itself is built on a solid foundation of <strong>HTML</strong>, ensuring semantic and well-structured content, while <strong>CSS</strong> was used to create a responsive and visually appealing user interface. We adopted agile methodologies throughout the semester, working in iterative sprints that allowed us to continuously refine our design and functionality. Regular sprint meetings and testing cycles helped us improve the code quality and user experience.
  </p>

  <!-- Future Enhancements -->
  <h2>Future Enhancements</h2>
  <p>
    While SmartSummaries is a robust tool for our project, we see opportunities for growth in the future:
  </p>
  <ul>
    <li><strong>User Feedback Loop:</strong> Develop a system to gather and analyze user feedback, ensuring continuous improvement and adaptation to user needs.</li>
    <li><strong>Mobile Expansion:</strong> Build a mobile version and have that working on iOS and Android as we realize a large percentage of our user base will be using social media on their phones.</li>
    <li><strong>Customization Options:</strong> Allow users to personalize summaries by adjusting tone for their posts.</li>
  </ul>

</div>

<!-- Footer -->
<footer class="bg-light text-center p-4">
  <p>&copy; <?php echo date("Y"); ?> SmartSummaries. All Rights Reserved.</p>
</footer>

</body>
</html>
