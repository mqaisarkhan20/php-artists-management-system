<nav class="navbar bg-primary navbar-dark navbar-expand-sm">
  <div class="container">
    <!-- Links -->
    <ul class="nav navbar-nav" style="visibility: visible;">
      <li class="nav-item">
        <a class="nav-link <?= ($nav_active == 'events') ? 'active': false; ?>" href="<?= URL ?>">Events</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($nav_active == 'artists') ? 'active': false; ?>" href="<?= URL ?>artist.php">Artists</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($nav_active == 'slots') ? 'active': false; ?>" href="<?= URL ?>slot.php">Slots</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($nav_active == 'artist_fees') ? 'active': false; ?>" href="<?= URL ?>artist_fees.php">Fees</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($nav_active == 'booking_offer') ? 'active': false; ?>" href="<?= URL ?>booking_offer.php">Booking</a>
      </li>
    </ul>

    <ul class="navbar-nav">
      <?php if (isset($_SESSION['username'])): ?>
      <li class="nav-item">
        <a class="nav-link" href="?logout">Logout</a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>