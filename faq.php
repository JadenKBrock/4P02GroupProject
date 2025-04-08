<?php 
// faq.php
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'domain' => $_SERVER['HTTP_HOST'],
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();
$page_title = "FAQ";
include "./views/header.php";
?>
<!-- Hero Section -->
<div class="hero" style="background: url('assets/images/hero_faq.jpg') no-repeat center center; background-size: cover; height: 200px; position: relative; color: #fff;">
  <div class="hero-overlay" style="background: rgba(0,0,0,0.5); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
  <div class="hero-content" style="position: relative; z-index: 2; padding: 60px 20px; text-align: center;">
    <h1>Frequently Asked Questions</h1>
    <p>Below you'll find answers to some of the most common questions we receive. If you need further assistance, feel free to reach out to us.</p>
  </div>
</div>



<!-- FAQ Accordion -->
<div class="container section" style="padding: 40px 20px;">
  <div class="accordion" id="faqAccordion">
    <!-- FAQ Item 1 -->
    <div class="card">
      <div class="card-header" id="faqHeadingOne">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
            How does SmartSummaries work?
          </button>
        </h2>
      </div>
      <div id="faqCollapseOne" class="collapse show" aria-labelledby="faqHeadingOne" data-parent="#faqAccordion">
        <div class="card-body">
          Our platform accepts your search keyword, retrieves the most relevant links from trusted sources, and then uses advanced summarization algorithms to convert detailed content into concise, shareable posts. This process happens in real time, ensuring you receive up-to-date summaries on whichever link you like.
        </div>
      </div>
    </div>
    <!-- FAQ Item 2 -->
    <div class="card">
      <div class="card-header" id="faqHeadingTwo">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
            What social media platforms can I share to?
          </button>
        </h2>
      </div>
      <div id="faqCollapseTwo" class="collapse" aria-labelledby="faqHeadingTwo" data-parent="#faqAccordion">
        <div class="card-body">
          SmartSummaries is currently optimized for Facebook and Twitter. Our team is actively working on integrating additional platforms, ensuring you can share your content wherever you need.
        </div>
      </div>
    </div>
    <!-- FAQ Item 3 -->
    <div class="card">
      <div class="card-header" id="faqHeadingThree">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
            Do I need an account to use SmartSummaries
          </button>
        </h2>
      </div>
      <div id="faqCollapseThree" class="collapse" aria-labelledby="faqHeadingThree" data-parent="#faqAccordion">
        <div class="card-body">
          SmartSummaries does not require an account to use the site. You can use the generate page to retrieve links and summaries with being logged in. However, if you want to save posts to your dashboard or share them directly to social media, you'll need to log in. Once logged in, your dashboard will store your saved posts, and you can access and post them directly from the website each time you log in.
        </div>
      </div>
    </div>
    <!-- FAQ Item 4 -->
    <div class="card">
      <div class="card-header" id="faqHeadingFour">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
            Can I edit the summaries before sharing?
          </button>
        </h2>
      </div>
      <div id="faqCollapseFour" class="collapse" aria-labelledby="faqHeadingFour" data-parent="#faqAccordion">
        <div class="card-body">
          Absolutely! Once a summary is generated, you have the option to edit and customize it. This allows you to add personal touches, or include additional context before posting.
        </div>
      </div>
    </div>
    <!-- FAQ Item 5 -->
    <div class="card">
      <div class="card-header" id="faqHeadingFive">
        <h2 class="mb-0">
          <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapseFive" aria-expanded="false" aria-controls="faqCollapseFive">
            Who do I contact for further questions or support?
          </button>
        </h2>
      </div>
      <div id="faqCollapseFive" class="collapse" aria-labelledby="faqHeadingFive" data-parent="#faqAccordion">
        <div class="card-body">
          If you have any additional questions or need personalized support, please email our team at. 
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-light text-center p-4">
  <p>&copy; <?php echo date("Y"); ?> SmartSummaries. All Rights Reserved.</p>
</footer>

</body>
</html>
