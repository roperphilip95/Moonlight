// WhatsApp launcher + small helpers
function openWhatsApp(phone, text){
  const msg = encodeURIComponent(text || "Hello Moonlight VIP Lounge");
  const link = `https://wa.me/${phone}?text=${msg}`;
  window.open(link, "_blank");
}
function copyToClipboard(txt){
  navigator.clipboard.writeText(txt).then(()=>alert("Copied!"));
}
