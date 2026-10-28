const risks = [{
    id: 'R-001',
    title: 'Gangguan layanan sistem kritikal dan serangan siber',
    owner: 'Manager IT',
    dir: 'Teknologi Informasi',
    cat: 'Teknologi',
    inherent: 25,
    residual: 20,
    appetite: 'Melebihi',
    progress: 42,
    due: '30 Sep 2026',
    decision: 'Persetujuan percepatan implementasi DR site dan managed SOC.',
    mitigation: 'Implementasi DR site, EDR endpoint, SOC 24/7 dan simulasi incident response.'
}, {
    id: 'R-002',
    title: 'Target okupansi Sosoro Mall tidak tercapai',
    owner: 'VP Komersial',
    dir: 'Komersial',
    cat: 'Strategis',
    inherent: 20,
    residual: 16,
    appetite: 'Melebihi',
    progress: 55,
    due: '15 Okt 2026',
    decision: 'Persetujuan skema commercial incentive untuk anchor tenant.',
    mitigation: 'Repricing tenant mix, akuisisi anchor tenant, dan digital campaign berbasis conversion.'
}, {
    id: 'R-003',
    title: 'Piutang usaha melewati jatuh tempo',
    owner: 'VP Keuangan',
    dir: 'Keuangan',
    cat: 'Keuangan',
    inherent: 20,
    residual: 15,
    appetite: 'Melebihi',
    progress: 68,
    due: '25 Sep 2026',
    decision: 'Mandat eskalasi collection dan restrukturisasi selektif.',
    mitigation: 'Aging control mingguan, payment reminder otomatis, dan collection task force.'
}, {
    id: 'R-004',
    title: 'Ketergantungan pendapatan pada pelanggan utama',
    owner: 'Direktur Komersial',
    dir: 'Komersial',
    cat: 'Strategis',
    inherent: 16,
    residual: 12,
    appetite: 'Mendekati',
    progress: 35,
    due: '30 Nov 2026',
    decision: '',
    mitigation: 'Diversifikasi portofolio dan target kontribusi revenue eksternal.'
}, {
    id: 'R-005',
    title: 'Kegagalan SLA layanan kebersihan dan keamanan',
    owner: 'VP Operasional',
    dir: 'Operasional',
    cat: 'Operasional',
    inherent: 16,
    residual: 12,
    appetite: 'Mendekati',
    progress: 76,
    due: '10 Okt 2026',
    decision: '',
    mitigation: 'Digital SLA monitoring, vendor scorecard, dan corrective action.'
}, {
    id: 'R-006',
    title: 'Ketidakpatuhan perizinan dan regulasi properti',
    owner: 'Corporate Secretary',
    dir: 'Operasional',
    cat: 'Kepatuhan',
    inherent: 15,
    residual: 10,
    appetite: 'Dalam',
    progress: 82,
    due: '20 Okt 2026',
    decision: '',
    mitigation: 'Compliance calendar dan legal document repository.'
}, {
    id: 'R-007',
    title: 'Keterlambatan proyek revitalisasi aset',
    owner: 'VP Property',
    dir: 'Operasional',
    cat: 'Operasional',
    inherent: 15,
    residual: 9,
    appetite: 'Dalam',
    progress: 64,
    due: '15 Des 2026',
    decision: '',
    mitigation: 'Stage gate proyek dan weekly project control.'
}, {
    id: 'R-008',
    title: 'Kesenjangan kompetensi digital tenaga kerja',
    owner: 'VP Human Capital',
    dir: 'Human Capital',
    cat: 'Strategis',
    inherent: 12,
    residual: 8,
    appetite: 'Dalam',
    progress: 60,
    due: '31 Des 2026',
    decision: '',
    mitigation: 'Digital capability mapping dan targeted upskilling.'
}];
const colors = s => s >= 20 ? '#d93b45' : s >= 15 ? '#ed8351' : s >= 10 ? '#f3a62a' : s >= 5 ? '#d5bd3e' : '#1c9b68';
const riskList = document.getElementById('topRisks');

function renderTop(data = risks) {
    riskList.innerHTML = data.slice().sort((a, b) => b.residual - a.residual).slice(0, 5).map((r, i) => `<div class="risk-item" data-id="${r.id}"><span class="rank">${i+1}</span><div><strong>${r.title}</strong><small>${r.id} · ${r.owner}</small></div><span class="score" style="background:${colors(r.residual)}">${r.residual}</span></div>`).join('');
    riskList.querySelectorAll('.risk-item').forEach(x => x.onclick = () => openRisk(x.dataset.id))
}

function renderTable(data = risks) {
    document.getElementById('riskTable').innerHTML = data.map(r => `<tr><td><b>${r.id}</b></td><td>${r.title}</td><td>${r.owner}</td><td><span class="pill">${r.cat}</span></td><td><span class="pill score-pill" style="background:${colors(r.inherent)}">${r.inherent}</span></td><td><span class="pill score-pill" style="background:${colors(r.residual)}">${r.residual}</span></td><td><span class="status ${r.appetite==='Melebihi'?'critical':r.appetite==='Mendekati'?'warning':''}">${r.appetite}</span></td><td><button class="table-btn" data-id="${r.id}">Detail</button></td></tr>`).join('');
    document.querySelectorAll('.table-btn').forEach(x => x.onclick = () => openRisk(x.dataset.id))
}

function renderMitigations(data = risks) {
    document.getElementById('mitigationCards').innerHTML = data.map(r => `<article class="mit-card"><span class="status ${r.progress<60?'critical':r.progress<75?'warning':''}">${r.id}</span><h3>${r.title}</h3><p>${r.mitigation}</p><div class="mit-meta"><span>${r.owner}</span><b>${r.progress}%</b></div><div class="progress"><span style="width:${r.progress}%;background:${r.progress<60?'#d93b45':r.progress<75?'#f3a62a':'#1c9b68'}"></span></div><div class="mit-meta"><small>Target</small><strong>${r.due}</strong></div></article>`).join('')
}

function renderKri() {
    const k = [
        ['System availability', '98,2%', '≥ 99,5%', 'Teknologi Informasi', 'Kritis'],
        ['Occupancy rate', '61%', '≥ 75%', 'Komersial', 'Waspada'],
        ['Overdue receivables', 'Rp8,7 M', '≤ Rp5 M', 'Keuangan', 'Kritis'],
        ['SLA vendor achievement', '91%', '≥ 95%', 'Operasional', 'Waspada'],
        ['Employee turnover', '4,1%', '≤ 8%', 'Human Capital', 'Normal'],
        ['Project delay index', '7,5%', '≤ 10%', 'Operasional', 'Normal']
    ];
    document.getElementById('kriBoard').innerHTML = k.map(x => `<article class="kri-card"><span class="status ${x[4]==='Kritis'?'critical':x[4]==='Waspada'?'warning':''}">${x[4].toUpperCase()}</span><h3>${x[0]}</h3><strong style="font-size:25px">${x[1]}</strong><p>Threshold: ${x[2]} · ${x[3]}</p></article>`).join('')
}

function heatmap() {
    const h = document.getElementById('heatmap');
    let html = '';
    for (let imp = 1; imp <= 5; imp++)
        for (let lik = 1; lik <= 5; lik++) {
            const score = imp * lik,
                count = risks.filter(r => r.inherent === score).length,
                cl = score >= 15 ? 'extreme' : score >= 10 ? 'high' : score >= 5 ? 'moderate' : 'low';
            html += `<div class="cell ${cl}" data-score="${score}" title="Skor ${score}">${count?`<em>${count}</em>`:''}</div>`
        }
    h.innerHTML = html;
    h.querySelectorAll('.cell').forEach(c => c.onclick = () => {
        const found = risks.filter(r => r.inherent == c.dataset.score);
        if (found.length) openRisk(found[0].id)
    })
}

function trend() {
    const months = ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'],
        a = [82, 86, 81, 88, 91, 94],
        b = [58, 61, 56, 53, 57, 54];
    const points = v => v.map((n, i) => `${25+i*66},${170-(n-40)*2}`).join(' ');
    document.getElementById('trendChart').innerHTML = `<svg viewBox="0 0 390 185" role="img" aria-label="Tren skor eksposur"><g stroke="#e7ebf0" stroke-width="1">${[30,70,110,150].map(y=>`<line x1="24" y1="${y}" x2="370" y2="${y}"/>`).join('')}</g><line x1="24" y1="120" x2="370" y2="120" stroke="#d93b45" stroke-dasharray="5 5"/><polyline fill="none" stroke="#0a2342" stroke-width="3" points="${points(a)}"/><polyline fill="none" stroke="#00a89d" stroke-width="3" points="${points(b)}"/>${months.map((m,i)=>`<text x="${25+i*66}" y="182" text-anchor="middle" font-size="9" fill="#7b8797">${m}</text>`).join('')}${b.map((n,i)=>`<circle cx="${25+i*66}" cy="${170-(n-40)*2}" r="3" fill="#00a89d"/>`).join('')}</svg>`
}

function openRisk(id) {
    const r = risks.find(x => x.id === id);
    if (!r) return;
    document.getElementById('modalTitle').textContent = r.title;
    document.getElementById('modalBody').innerHTML = `<p>${r.mitigation}</p><div class="detail-grid"><div><small>Risk owner</small><strong>${r.owner}</strong></div><div><small>Direktorat</small><strong>${r.dir}</strong></div><div><small>Inherent score</small><strong>${r.inherent} · Ekstrem</strong></div><div><small>Residual score</small><strong>${r.residual}</strong></div><div><small>Progres mitigasi</small><strong>${r.progress}%</strong></div><div><small>Target selesai</small><strong>${r.due}</strong></div></div>${r.decision?`<div class="decision"><strong>Keputusan yang dibutuhkan dari BOD</strong><br>${r.decision}</div>`:''}`;
    document.getElementById('modal').classList.add('show')
}

function changeView(id) {
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active-view'));
    document.getElementById(id).classList.add('active-view');
    document.querySelectorAll('.nav').forEach(n => n.classList.toggle('active', n.dataset.view === id));
    document.querySelector('.sidebar').classList.remove('open')
}
document.querySelectorAll('.nav').forEach(n => n.onclick = () => changeView(n.dataset.view));
document.querySelectorAll('[data-jump]').forEach(n => n.onclick = () => changeView(n.dataset.jump));
document.querySelector('.menu').onclick = () => document.querySelector('.sidebar').classList.toggle('open');
document.getElementById('closeModal').onclick = () => document.getElementById('modal').classList.remove('show');
document.getElementById('modal').onclick = e => {
    if (e.target.id === 'modal') e.currentTarget.classList.remove('show')
};
document.getElementById('showCritical').onclick = () => openRisk('R-001');
document.getElementById('search').oninput = e => renderTable(risks.filter(r => (r.title + r.owner + r.id).toLowerCase().includes(e.target.value.toLowerCase())));

function applyFilter() {
    const d = document.getElementById('directorate').value,
        c = document.getElementById('category').value;
    const f = risks.filter(r => (d === 'all' || r.dir === d) && (c === 'all' || r.cat === c));
    renderTop(f);
    document.getElementById('totalRisk').textContent = f.length === risks.length ? '42' : f.length;
    document.getElementById('criticalRisk').textContent = f.filter(r => r.residual >= 15).length
}
document.getElementById('directorate').onchange = applyFilter;
document.getElementById('category').onchange = applyFilter;
document.getElementById('reset').onclick = () => {
    document.getElementById('directorate').value = 'all';
    document.getElementById('category').value = 'all';
    applyFilter()
};
document.getElementById('export').onclick = () => {
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2600)
};
heatmap();
trend();
renderTop();
renderTable();
renderMitigations();
renderKri();