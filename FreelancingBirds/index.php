<?php include("includes/header.php"); ?>
<div class="hero">
    <h1>Find the perfect freelance services for your business</h1>
    <form action="buyer/browse_gigs.php" method="get">
        <input type="text" name="search" placeholder="Try 'logo design'" style="padding:10px;width:300px;">
        <button type="submit" style="padding:10px 15px;background:#FFD700;border:none;">Search</button>
    </form>
</div>

<h2 style="text-align:center;margin:30px 0;">Popular Gigs</h2>
<div class="gigs-container">
    <div class="gig-card">
        <img src="https://source.unsplash.com/400x300/?design,logo" alt="Gig">
        <div class="info">
            <h3>Logo Design</h3>
            <p>Professional logo design services starting at $20.</p>
            <button>Order Now</button>
        </div>
    </div>

    <div class="gig-card">
        <img src="https://source.unsplash.com/400x300/?web,development" alt="Gig">
        <div class="info">
            <h3>Web Development</h3>
            <p>Build responsive websites with modern UI/UX.</p>
            <button>Order Now</button>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>