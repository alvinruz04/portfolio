<?php
// frontend/public/tv/queue_board.php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="icon" href="/VIPCARWASH/frontend/public/favicon.ico" />
  <title>VIP Auto Spa • Queue Board</title>

  <style>
    :root{
      --bg0:#060913;
      --bg1:#070b16;
      --bg2:#0b1430;

      --panel: rgba(15, 18, 28, .62); /* like bg-neutral-900/60 */
      --panel2: rgba(0,0,0,.20);

      --stroke: rgba(255,255,255,.10);
      --stroke2: rgba(255,255,255,.06);

      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.55);

      --emerald: rgba(16,185,129,.18);
      --emeraldStroke: rgba(16,185,129,.28);

      --shadow: 0 18px 60px rgba(0,0,0,.55);
    }

    *{ box-sizing:border-box; }
    html, body { height:100%; margin:0; }
    body{
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: var(--text);
      background:
        radial-gradient(1200px 700px at 20% 10%, rgba(255,255,255,.06), transparent 55%),
        radial-gradient(900px 650px at 85% 5%, rgba(16,185,129,.10), transparent 58%),
        linear-gradient(135deg, var(--bg1), var(--bg2));
      overflow:hidden;
    }

    .wrap{
      height:100%;
      padding:22px;
      display:flex;
      flex-direction:column;
      gap:14px;
    }

    .topbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:12px;
      font-weight:600;
      letter-spacing:.10em;
      text-transform:uppercase;
      opacity:.92;
      white-space:nowrap;
    }

    .brandLogo{
      height: 70px;         
      width: auto;
      display:block;
      filter: drop-shadow(0 6px 18px rgba(0,0,0,.35));
    }

    .brandText{
      font-weight:600;
      letter-spacing:.10em;
      text-transform:uppercase;
      opacity:.92;
      white-space:nowrap;
    }

    .meta{
      display:flex;
      align-items:center;
      gap:12px;
      color: var(--muted);
      font-size:13px;
    }

    .pill{
      padding:8px 12px;
      border:1px solid var(--stroke);
      border-radius:999px;
      background: rgba(255,255,255,.04);
      display:flex;
      gap:10px;
      align-items:center;
      backdrop-filter: blur(8px);
      font-size:18px;
      font-weight:600;
      letter-spacing:.08em;
      color: var(--text);
    }

    .btn{
      appearance:none;
      border:1px solid var(--stroke);
      background: rgba(255,255,255,.06);
      color: var(--text);
      padding:9px 12px;
      border-radius:12px;
      cursor:pointer;
      font-weight:800;
      transition:.15s ease;
    }
    .btn:hover{ background: rgba(255,255,255,.10); }
    .btn:active{ transform: translateY(1px); }

    .grid{
      flex:1;
      min-height:0;
      display:grid;
      grid-template-columns: 1fr 1fr; /* left: now serving, right: waiting */
      gap:14px;
    }

    .card{
      border:1px solid var(--stroke);
      background: var(--panel);
      border-radius: 22px;
      box-shadow: var(--shadow);
      overflow:hidden;
      display:flex;
      flex-direction:column;
      min-height:0;
    }

    .cardHead{
      padding:16px 18px;
      border-bottom:1px solid var(--stroke);
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap:12px;
      background: linear-gradient(180deg, rgba(255,255,255,.04), transparent);
    }

    .cardTitle{
      font-size:24px;               /* premium: less shouty */
      letter-spacing:.14em;
      font-weight:650;
      text-transform:uppercase;
    }

    .sub{
      margin-top:4px;
      color: var(--muted);
      font-size:12px;
    }

    /* NOW SERVING */
    .nowHead{
      border-bottom:1px solid var(--emeraldStroke);
      background: linear-gradient(135deg, rgba(16,185,129,.20), rgba(16,185,129,.08));
    }

    .nowWrap{
      padding:14px;
      overflow:auto;
      min-height:0;
    }

    .nowRow{
      border:1px solid var(--emeraldStroke);
      background: rgba(16,185,129,.10);
      border-radius:18px;
      padding:16px 16px;
      display:grid;
      grid-template-columns: 120px 1fr;
      gap:14px;
      align-items:center;
      margin-bottom:12px;
    }

    .bay{
      font-size:18px;
      font-weight:650;
      letter-spacing:.16em;
      text-transform:uppercase;
      color: rgba(255,255,255,.88);
    }

    .nowRight{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:16px;
      min-width:0;
    }

    .nowPlate{
      font-size:35px;
      font-weight:650;
      letter-spacing:.04em;
      line-height:1.05;
    }

    .nowPkg{
      font-size:14px;
      color: rgba(255,255,255,.75);
      text-align:right;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      max-width: 380px;
    }

    /* WAITING TABLE */
    .tableWrap{
      overflow:auto;
      min-height:0;
    }

    .table{
      width:100%;
      border-collapse:collapse;
      font-size:18px;
    }

    .thead th{
      text-align:left;
      padding:12px 16px;
      color: rgba(255,255,255,.55);
      font-size:12px;
      letter-spacing:.18em;
      text-transform:uppercase;
      border-bottom:1px solid var(--stroke);
      background: rgba(0,0,0,.10);
      position: sticky;
      top: 0;
      backdrop-filter: blur(8px);
    }

    .tbody td{
      padding:14px 16px;
      border-top:1px solid var(--stroke2);
      vertical-align:middle;
    }

    .num{
      width:90px;
      color: rgba(255,255,255,.70);
      font-weight:600;
    }

    .plate{
      font-weight:500;
      letter-spacing:.03em;
    }

    .pkg{
      color: rgba(255,255,255,.75);
      max-width: 520px;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
    }

    .empty{
      padding:18px 16px;
      color: rgba(255,255,255,.55);
      font-size:16px;
    }

    .footer{
      display:flex;
      align-items:center;
      justify-content:space-between;
      color: rgba(255,255,255,.45);
      font-size:12px;
      padding-top:2px;
    }

    ::-webkit-scrollbar { height:10px; width:10px; }
    ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 999px; }

    @media (max-width: 1000px) {
      .grid { grid-template-columns: 1fr; }
      .brandLogo { height: 54px; }
      .pill { font-size: 16px; }
    }

    .wrap {
      padding: max(22px, env(safe-area-inset-top)) max(22px, env(safe-area-inset-right))
      max(22px, env(safe-area-inset-bottom)) max(22px, env(safe-area-inset-left));
    }
  </style>
</head>

<body>
  <div class="wrap" id="root">
    <div class="topbar">
      <div class="brand">
      <img src="/VIPCARWASH/frontend/public/assets/vip-logo-white.png"
       alt="VIP Auto Spa"
       class="brandLogo"
       draggable="false">
        <!-- VIP AUTO SPA -->
        <!-- <span style="opacity:.5;font-weight:500;">•</span> -->
        <span id="branchName" style="opacity:.78; font-weight:600;">Branch</span>
      </div>

      <div class="meta">
        <div class="pill">
          <span id="clock">--:--:-- -- ---</span>
        </div>
        <button class="btn" id="btnFull">Fullscreen</button>
      </div>
    </div>

    <div class="grid">
      <!-- LEFT: NOW SERVING -->
      <div class="card">
        <div class="cardHead nowHead">
          <div>
            <div class="cardTitle">NOW SERVING</div>
            <div class="sub" id="servingCount">0 washing</div>
          </div>
        </div>
        <div class="nowWrap" id="nowWrap">
          <div class="empty">Loading...</div>
        </div>
      </div>

      <!-- RIGHT: WAITING LIST -->
      <div class="card">
        <div class="cardHead">
          <div>
            <div class="cardTitle">WAITING LIST</div>
            <div class="sub" id="waitingCount">0 waiting</div>
          </div>
        </div>

        <div class="tableWrap">
          <table class="table">
            <thead class="thead">
              <tr>
                <th class="num">Queue</th>
                <th>Plate</th>
                <th>Package</th>
              </tr>
            </thead>
            <tbody class="tbody" id="waitingBody">
              <tr><td colspan="3" class="empty">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="footer">
      <div id="hint"></div>
      <div id="err" style="color: rgba(255, 120, 120, .9); font-weight:500;"></div>
    </div>
  </div>

<script>
(() => {
  const qs = new URLSearchParams(location.search);
  const branchId = qs.get('branch_id');
  const token = qs.get('k');

  const branchNameEl = document.getElementById('branchName');
  const clockEl = document.getElementById('clock');
  const updatedEl = document.getElementById('updated');
  const errEl = document.getElementById('err');
  const hintEl = document.getElementById('hint');

  const waitingBody = document.getElementById('waitingBody');
  const waitingCount = document.getElementById('waitingCount');
  const nowWrap = document.getElementById('nowWrap');
  const servingCount = document.getElementById('servingCount');

  const btnFull = document.getElementById('btnFull');

  // IMPORTANT: call the gateway in the SAME folder: /frontend/public/tv/queue.php
  const API_URL = `queue.php?branch_id=${encodeURIComponent(branchId || '')}&k=${encodeURIComponent(token || '')}&limit=20`;

  let timer = null;
  const activeIntervalMs = 7000;
  const hiddenIntervalMs = 20000;

  function fmtTime(date = new Date()) {
    let hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2,'0');
    const seconds = String(date.getSeconds()).padStart(2,'0');

    const ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;

    const hh = String(hours).padStart(2,'0');

    const days = ['SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'];
    const day = days[date.getDay()];

    return `${hh}:${minutes}:${seconds} ${ampm} ${day}`;
  }

function updateClock() {
  const now = new Date();

  let h = now.getHours();
  const m = String(now.getMinutes()).padStart(2,'0');
  const s = String(now.getSeconds()).padStart(2,'0');

  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12;
  if (h === 0) h = 12;

  const hh = String(h).padStart(2,'0');

  const days = [
    'SUNDAY','MONDAY','TUESDAY','WEDNESDAY',
    'THURSDAY','FRIDAY','SATURDAY'
  ];

  const day = days[now.getDay()];

  clockEl.textContent = `${hh}:${m}:${s} ${ampm} ${day}`;
}
  updateClock();
  setInterval(updateClock, 1000);

  function escapeHtml(s) {
    return String(s)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  async function fetchQueue() {
    if (!branchId || !token) {
      errEl.textContent = 'Missing branch_id or token in URL.';
      hintEl.textContent = 'Example: /tv/queue_board.php?branch_id=1&k=YOUR_TOKEN';
      return;
    }

    try {
      errEl.textContent = '';
      hintEl.textContent = '';

      const res = await fetch(API_URL, { cache: 'no-store' });
      const text = await res.text();

      let data = null;
      try { data = JSON.parse(text); }
      catch {
        errEl.textContent = 'API returned non-JSON. Check PHP warnings.';
        console.log('RAW RESPONSE:', text);
        return;
      }

      if (!res.ok || !data.success) {
        errEl.textContent = data.message || `Error (${res.status})`;
        return;
      }

      const rawBranch = (data.branch?.name || `Branch #${branchId}`).trim();
      branchNameEl.textContent = `${rawBranch.toUpperCase()} BRANCH`;
      // updatedEl.textContent = 'Last updated: ' + fmtTime(new Date());

      // NOW SERVING (LEFT)
      const ns = Array.isArray(data.now_serving) ? data.now_serving : [];
      servingCount.textContent = `${ns.length} washing`;

      if (ns.length === 0) {
        nowWrap.innerHTML = `<div class="empty">No cars currently WASHING.</div>`;
      } else {
        nowWrap.innerHTML = ns.map(row => `
          <div class="nowRow">
            <div>
              <div class="bay">${escapeHtml(row.bay_label || ('BAY-' + String(row.bay_no || '')))}</div>
              <div class="sub">Queue #${Number(row.queue_no || 0)}</div>
            </div>
            <div class="nowRight">
              <div class="nowPlate">${escapeHtml(row.plate_no || '—')}</div>
              <div class="nowPkg" title="${escapeHtml(row.package || '—')}">${escapeHtml(row.package || '—')}</div>
            </div>
          </div>
        `).join('');
      }

      // WAITING LIST (RIGHT)
      const wl = Array.isArray(data.waiting_list) ? data.waiting_list : [];
      waitingCount.textContent = `${wl.length} waiting`;

      if (wl.length === 0) {
        waitingBody.innerHTML = `<tr><td colspan="3" class="empty">No cars in WAITING.</td></tr>`;
      } else {
        waitingBody.innerHTML = wl.map(r => `
          <tr>
            <td class="num">#${Number(r.queue_no || 0)}</td>
            <td class="plate">${escapeHtml(r.plate_no || '—')}</td>
            <td class="pkg" title="${escapeHtml(r.package || '—')}">${escapeHtml(r.package || '—')}</td>
          </tr>
        `).join('');
      }
    } catch (e) {
      errEl.textContent = 'Network/server error.';
    }
  }

  function scheduleNext() {
    clearTimeout(timer);
    const ms = document.hidden ? hiddenIntervalMs : activeIntervalMs;
    timer = setTimeout(async () => {
      await fetchQueue();
      scheduleNext();
    }, ms);
  }

  document.addEventListener('visibilitychange', () => scheduleNext());

  btnFull.addEventListener('click', async () => {
    const el = document.documentElement;
    try {
      if (!document.fullscreenElement) {
        await el.requestFullscreen();
        btnFull.textContent = 'Exit Fullscreen';
      } else {
        await document.exitFullscreen();
        btnFull.textContent = 'Fullscreen';
      }
    } catch (e) {
      errEl.textContent = 'Fullscreen not supported or blocked by the device/browser.';
    }
  });

  document.addEventListener('fullscreenchange', () => {
    btnFull.textContent = document.fullscreenElement ? 'Exit Fullscreen' : 'Fullscreen';
  });

  fetchQueue().then(scheduleNext);
})();
</script>
</body>
</html>