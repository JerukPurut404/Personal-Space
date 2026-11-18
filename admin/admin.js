// Add / remove rows in any .repeater (projects links, socials, translations)
document.querySelectorAll('.repeater').forEach((rep) => {
  const rows = rep.querySelector('.repeater__rows');
  const tpl  = rep.querySelector('template');
  const add  = rep.querySelector('.repeater__add');

  add?.addEventListener('click', () => {
    const key = Math.random().toString(36).slice(2, 10);
    rows.insertAdjacentHTML('beforeend', tpl.innerHTML.replaceAll('__i__', key));
  });

  rep.addEventListener('click', (e) => {
    const btn = e.target.closest('.repeater__remove');
    if (btn) btn.closest('.repeater__row').remove();
  });
});

// "Copy path" buttons in media
document.querySelectorAll('[data-copy]').forEach((btn) => {
  btn.addEventListener('click', () => navigator.clipboard.writeText(btn.dataset.copy));
});