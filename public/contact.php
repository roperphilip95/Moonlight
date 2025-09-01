<?php include __DIR__ . '/_partials_header.php'; ?>
<h2>Contact & Reservations</h2>
<div class="grid grid-2">
  <div class="card">
    <h3>Message us on WhatsApp</h3>
    <p class="muted">Fastest way to book VIP tables and ask about events.</p>
    <form onsubmit="openWhatsApp(document.getElementById('wa_phone').value, document.getElementById('wa_msg').value); return false;">
      <div class="row">
        <label>Phone (with country code)
          <input id="wa_phone" value="2348012345678">
        </label>
        <label>Preferred Date
          <input id="wa_date" type="date" onchange="document.getElementById('wa_msg').value=`Reservation request for ${this.value}`">
        </label>
      </div>
      <label>Message
        <textarea id="wa_msg" rows="4">I want to reserve a VIP table at Moonlight.</textarea>
      </label>
      <p><button class="btn" type="submit">Open WhatsApp</button></p>
    </form>
  </div>
  <div class="card">
    <h3>Location & Hours</h3>
    <p class="muted">We’re open daily till late.</p>
    <p class="tag">Happy Hour 6–8pm</p>
    <p class="tag">Dress Smart</p>
    <p><span class="kbd">Press</span> to copy address:</p>
    <p><a class="btn" href="javascript:copyToClipboard('Moonlight VIP Lounge, Lagos');">Copy Address</a></p>
  </div>
</div>
<?php include __DIR__ . '/_partials_footer.php'; ?>
