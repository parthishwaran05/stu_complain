async function loadComplaints() {
    const status = document.getElementById('filter-status').value;
    const category = document.getElementById('filter-category').value;
    const from = document.getElementById('filter-from').value;
    const to = document.getElementById('filter-to').value;

    const params = new URLSearchParams({ status, category, from, to });
    const base = window.APP_BASE || '';
    const res = await fetch(`${base}/modules/complaints/ajax_list.php?${params.toString()}`);
    const data = await res.json();

    const tbody = document.getElementById('complaints-body');
    tbody.innerHTML = '';

    data.forEach(item => {
        const statusClass = `status-${item.status.toLowerCase().replace(/\s+/g, '-')}`;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.complaint_uid}</td>
            <td>${item.subject}</td>
            <td>${item.category}</td>
            <td>${item.priority}</td>
            <td><span class="badge ${statusClass}">${item.status}</span></td>
            <td>${item.updated_at}</td>
            <td><a class="btn outline" href="${base}/modules/complaints/view.php?id=${item.id}">View</a></td>
        `;
        tbody.appendChild(tr);
    });
}

['filter-status','filter-category','filter-from','filter-to'].forEach(id => {
    document.getElementById(id).addEventListener('change', loadComplaints);
});

loadComplaints();
setInterval(loadComplaints, 20000);
