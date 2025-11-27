@extends('layout.main')
@section('title','Pipeline Management')

@section('content')
<style>
    .pipeline-item {
        padding: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .pipeline-item:hover {
        background-color: #f9fafb;
        border-left-color: #3b82f6;
        transform: translateX(4px);
    }

    .detail-label {
        font-size: 0.65rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.15rem;
        display: block;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 0.85rem;
        color: #111827;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .badge-status {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-success { background-color: #d1fae5; color: #065f46; }
    .badge-danger { background-color: #fee2e2; color: #991b1b; }
    .badge-info { background-color: #dbeafe; color: #0c4a6e; }

    [x-cloak] { display: none !important; }

    #detailModal {
        scroll-behavior: smooth;
    }

    #modalContent::-webkit-scrollbar {
        width: 6px;
    }

    #modalContent::-webkit-scrollbar-track {
        background: transparent;
    }

    #modalContent::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    #modalContent::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .pipeline-header {
        padding: 0.75rem;
        font-weight: 600;
        font-size: 0.85rem;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pipeline-count {
        background: rgba(255,255,255,0.2);
        padding: 0.2rem 0.45rem;
        border-radius: 0.25rem;
        font-size: 0.65rem;
    }

    .pipeline-content {
        max-height: 500px;
        overflow-y: auto;
    }

    .pipeline-item h4 {
        margin: 0;
        font-weight: 600;
        color: #111827;
        font-size: 0.8rem;
        line-height: 1.3;
    }

    .pipeline-item small {
        color: #6b7280;
        display: block;
        margin-top: 0.2rem;
        font-size: 0.7rem;
    }
</style>

<div class="container-expanded mx-auto px-4 lg:px-8 py-8 pt-[60px] mt-4">
    <div style="background:#fff;border-radius:0.5rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);border:1px solid #e5e7eb;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:1.125rem;font-weight:600;color:#111827;margin:0;">Pipeline Management</h3>
            <p style="font-size:0.8rem;color:#6b7280;margin-top:0.15rem;">Track your leads, visits, proposals, and follow-ups</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(5, 1fr);min-height:500px;">
          <!-- LEAD STAGE -->
            <div style="border-right:1px solid #e5e7eb;">
                <div style="padding:0.875rem;font-weight:600;font-size:0.85rem;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fas fa-user-plus"></i> Leads</span>
                    <span style="background:rgba(255,255,255,0.2);padding:0.2rem 0.45rem;border-radius:0.25rem;font-size:0.65rem;">{{ $leads->count() }}</span>
                </div>
                <div style="max-height:500px;overflow-y:auto;">
                    @forelse($leads as $lead)
                    <div class="pipeline-item" onclick="openModal('lead', {{ $lead->id }})">
                        <h4 style="margin:0;font-weight:600;color:#111827;font-size:0.8rem;line-height:1.3;">{{ $lead->company->company_name ?? '-' }}</h4>
                        <small style="color:#6b7280;display:block;margin-top:0.2rem;font-size:0.7rem;">
                            <i class="fas fa-user" style="width:12px;"></i> {{ $lead->pic_name }}
                        </small>
                        <small style="color:#6b7280;display:block;margin-top:0.15rem;font-size:0.7rem;">
                            <i class="fas fa-calendar" style="width:12px;"></i> {{ $lead->visit_date->format('d/m/Y') }}
                        </small>
                    </div>
                    @empty
                    <div style="padding:1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">Tidak ada leads</div>
                    @endforelse
                </div>
            </div>

            <!-- VISIT STAGE -->
            <div style="border-right:1px solid #e5e7eb;">
                <div style="padding:0.875rem;font-weight:600;font-size:0.85rem;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fas fa-map-pin"></i> Visit</span>
                    <span style="background:rgba(255,255,255,0.2);padding:0.2rem 0.45rem;border-radius:0.25rem;font-size:0.65rem;">{{ $visits->count() }}</span>
                </div>
                <div style="max-height:500px;overflow-y:auto;">
                    @forelse($visits as $visit)
                    <div class="pipeline-item" onclick="openModal('visit', {{ $visit->id }})">
                        <h4 style="margin:0;font-weight:600;color:#111827;font-size:0.8rem;line-height:1.3;">{{ $visit->company->company_name ?? '-' }}</h4>
                        <small style="color:#6b7280;display:block;margin-top:0.2rem;font-size:0.7rem;">
                            <i class="fas fa-user" style="width:12px;"></i> {{ optional($visit->sales)->username ?? '-' }}
                        </small>
                        <small style="color:#6b7280;display:block;margin-top:0.15rem;font-size:0.7rem;">
                            <i class="fas fa-calendar" style="width:12px;"></i> {{ $visit->visit_date->format('d/m/Y') }}
                        </small>
                    </div>
                    @empty
                    <div style="padding:1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">Tidak ada visit</div>
                    @endforelse
                </div>
            </div>

            <!-- PENGAJUAN PROPOSAL STAGE -->
            <div style="border-right:1px solid #e5e7eb;">
                <div style="padding:0.875rem;font-weight:600;font-size:0.85rem;background:linear-gradient(135deg,#ec4899,#db2777);color:#fff;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fas fa-file-alt"></i> Proposal</span>
                    <span style="background:rgba(255,255,255,0.2);padding:0.2rem 0.45rem;border-radius:0.25rem;font-size:0.65rem;">3</span>
                </div>
                <div style="max-height:500px;overflow-y:auto;">
                    <div class="pipeline-item" onclick="openModal('proposal', 1)">
                        <h4 style="margin:0;font-weight:600;color:#111827;font-size:0.8rem;line-height:1.3;">PT Maju Sejahtera</h4>
                        <small style="color:#6b7280;display:block;margin-top:0.2rem;font-size:0.7rem;">
                            <i class="fas fa-paper-plane" style="width:12px;"></i> Email
                        </small>
                        <small style="color:#6b7280;display:block;margin-top:0.15rem;font-size:0.7rem;">
                            <i class="fas fa-calendar" style="width:12px;"></i> 15/11/2024
                        </small>
                    </div>
                    <div class="pipeline-item" onclick="openModal('proposal', 2)">
                        <h4 style="margin:0;font-weight:600;color:#111827;font-size:0.8rem;line-height:1.3;">CV Digital Solutions</h4>
                        <small style="color:#6b7280;display:block;margin-top:0.2rem;font-size:0.7rem;">
                            <i class="fas fa-paper-plane" style="width:12px;"></i> WhatsApp
                        </small>
                        <small style="color:#6b7280;display:block;margin-top:0.15rem;font-size:0.7rem;">
                            <i class="fas fa-calendar" style="width:12px;"></i> 18/11/2024
                        </small>
                    </div>
                    <div class="pipeline-item" onclick="openModal('proposal', 3)">
                        <h4 style="margin:0;font-weight:600;color:#111827;font-size:0.8rem;line-height:1.3;">PT Teknologi Nusantara</h4>
                        <small style="color:#6b7280;display:block;margin-top:0.2rem;font-size:0.7rem;">
                            <i class="fas fa-paper-plane" style="width:12px;"></i> Door to Door
                        </small>
                        <small style="color:#6b7280;display:block;margin-top:0.15rem;font-size:0.7rem;">
                            <i class="fas fa-calendar" style="width:12px;"></i> 20/11/2024
                        </small>
                    </div>
                </div>
            </div>

            <!-- FOLLOW UP STAGE -->
            <div style="border-right:1px solid #e5e7eb;">
                <div style="padding:0.875rem;font-weight:600;font-size:0.85rem;background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fas fa-handshake"></i> Follow Up</span>
                    <span style="background:rgba(255,255,255,0.2);padding:0.2rem 0.45rem;border-radius:0.25rem;font-size:0.65rem;">{{ $followUps->count() }}</span>
                </div>
                <div style="max-height:500px;overflow-y:auto;">
                    @forelse($followUps as $followUp)
                    <div class="pipeline-item" onclick="openModal('followup', {{ $followUp->id }})">
                        <h4 style="margin:0;font-weight:600;color:#111827;font-size:0.8rem;line-height:1.3;">{{ optional($followUp->company)->company_name ?? '-' }}</h4>
                        <small style="color:#6b7280;display:block;margin-top:0.2rem;font-size:0.7rem;">
                            <i class="fas fa-user" style="width:12px;"></i> {{ optional($followUp->sales)->username ?? '-' }}
                        </small>
                        <small style="color:#6b7280;display:block;margin-top:0.15rem;font-size:0.7rem;">
                            <i class="fas fa-calendar" style="width:12px;"></i> {{ $followUp->visit_date->format('d/m/Y') }}
                        </small>
                    </div>
                    @empty
                    <div style="padding:1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">Tidak ada follow up</div>
                    @endforelse
                </div>
            </div>

            <!-- TRANSAKSI STAGE -->
            <div>
                <div style="padding:0.875rem;font-weight:600;font-size:0.85rem;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fas fa-file-invoice-dollar"></i> Transaksi</span>
                    <span style="background:rgba(255,255,255,0.2);padding:0.2rem 0.45rem;border-radius:0.25rem;font-size:0.65rem;">{{ $transaksi->count() }}</span>
                </div>
                <div style="max-height:500px;overflow-y:auto;">
                    @forelse($transaksi as $item)
                    <div class="pipeline-item" onclick="openModal('transaksi', {{ $item->id }})">
                        <h4 style="margin:0;font-weight:600;color:#111827;font-size:0.8rem;line-height:1.3;">{{ Str::limit($item->nama_perusahaan, 20) }}</h4>
                        <small style="color:#6b7280;display:block;margin-top:0.2rem;font-size:0.7rem;">
                            <i class="fas fa-money-bill-wave" style="width:12px;"></i> Rp {{ number_format($item->nilai_proyek,0,',','.') }}
                        </small>
                        <small style="color:#6b7280;display:block;margin-top:0.15rem;font-size:0.7rem;">
                            <i class="fas fa-user-tie" style="width:12px;"></i> {{ Str::limit($item->nama_sales, 15) }}
                        </small>
                        <span class="badge-status {{ $item->status == 'Deals' ? 'badge-success' : 'badge-danger' }}" style="margin-top:0.2rem;">
                            {{ $item->status }}
                        </span>
                    </div>
                    @empty
                    <div style="padding:1rem;text-align:center;color:#9ca3af;font-size:0.75rem;">Tidak ada transaksi</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div id="detailModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; background:rgba(0,0,0,0.5); overflow-y:auto;" onclick="closeModal(event)">
    <div style="display:flex; align-items:center; justify-content:center; min-height:100vh; padding:0.75rem;">
        <div onclick="event.stopPropagation()" style="background:white; border-radius:0.5rem; max-width:900px; width:100%; box-shadow:0 20px 25px -5px rgba(0,0,0,0.3);">
            <div style="background:linear-gradient(135deg,#3b82f6,#2563eb); padding:1rem; display:flex; justify-content:space-between; align-items:center; position: sticky; top: 0; z-index: 10; border-radius: 0.5rem 0.5rem 0 0;">
                <h3 style="color:white; font-size:1rem; font-weight:600; margin:0;">Detail</h3>
                <button onclick="closeModal()" style="color:white; background:none; border:none; cursor:pointer; font-size:1.25rem; padding: 0; width: 35px; height: 35px; display: flex; align-items:center; justify-content:center;">✕</button>
            </div>
            <div id="modalContent" style="padding:1.5rem; max-height: calc(100vh - 100px); overflow-y: auto;"></div>
        </div>
    </div>
</div>

<script>
function openModal(type, id) {
    const routes = {
        lead: `/pipeline/lead/${id}`,
        visit: `/pipeline/visit/${id}`,
        proposal: `/pipeline/proposal/${id}`,
        followup: `/pipeline/follow-up/${id}`,
        transaksi: `/pipeline/transaksi/${id}`
    };
    
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('modalContent');
    
    modal.style.display = 'block';
    content.innerHTML = '<div style="text-align:center; padding:2rem; color:#6b7280;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3b82f6; margin-bottom: 0.75rem;"></i><p style="margin:0;">Loading...</p></div>';
    
    if (type === 'proposal') {
        const dummyProposals = {
            1: {
                company: 'PT Maju Sejahtera',
                pic_name: 'Budi Santoso',
                pic_position: 'Procurement Manager',
                pic_phone: '081234567890',
                pic_email: 'budi.santoso@majusejahtera.com',
                sales_name: 'Ahmad Fauzi',
                sales_email: 'ahmad.fauzi@company.com',
                proposal_date: '15/11/2024',
                submission_method: 'Email',
                proposal_number: 'PROP/2024/11/001',
                proposal_value: 'Rp 150.000.000',
                proposal_title: 'Sistem Manajemen Inventory',
                status: 'Menunggu Review',
                notes: 'Proposal dikirim via email beserta company profile dan portfolio',
                created_at: '15 Nov 2024, 14:30'
            },
            2: {
                company: 'CV Digital Solutions',
                pic_name: 'Siti Nurhaliza',
                pic_position: 'Director',
                pic_phone: '081298765432',
                pic_email: 'siti@digitalsolutions.co.id',
                sales_name: 'Rina Wijaya',
                sales_email: 'rina.wijaya@company.com',
                proposal_date: '18/11/2024',
                submission_method: 'WhatsApp',
                proposal_number: 'PROP/2024/11/002',
                proposal_value: 'Rp 85.000.000',
                proposal_title: 'Website Development & Digital Marketing',
                status: 'Dalam Pembahasan',
                notes: 'Proposal PDF dikirim via WhatsApp Business, sudah dibaca oleh PIC',
                created_at: '18 Nov 2024, 10:15'
            },
            3: {
                company: 'PT Teknologi Nusantara',
                pic_name: 'Joko Widodo',
                pic_position: 'IT Manager',
                pic_phone: '081234509876',
                pic_email: 'joko.widodo@teknusantara.com',
                sales_name: 'Dedi Hermawan',
                sales_email: 'dedi.hermawan@company.com',
                proposal_date: '20/11/2024',
                submission_method: 'Door to Door',
                proposal_number: 'PROP/2024/11/003',
                proposal_value: 'Rp 250.000.000',
                proposal_title: 'Enterprise Resource Planning System',
                status: 'Menunggu Approval',
                notes: 'Proposal diserahkan langsung ke kantor klien, presentasi sudah dilakukan',
                created_at: '20 Nov 2024, 09:00'
            }
        };
        
        setTimeout(() => {
            const data = dummyProposals[id];
            if (data) {
                content.innerHTML = renderDetail('proposal', data);
            } else {
                content.innerHTML = showError('Data proposal tidak ditemukan');
            }
        }, 500);
        return;
    }
    
    fetch(routes[type])
        .then(res => {
            if (!res.ok) throw new Error('Network error: ' + res.status);
            return res.json();
        })
        .then(data => {
            if(data.success) {
                content.innerHTML = renderDetail(data.type, data.data);
            } else {
                content.innerHTML = showError(data.message || 'Gagal memuat data');
            }
        })
        .catch(err => {
            content.innerHTML = showError('Terjadi kesalahan: ' + err.message);
        });
}

function closeModal(event) {
    if (event && event.target.id !== 'detailModal') return;
    const modal = document.getElementById('detailModal');
    modal.style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

function renderDetail(type, data) {
    if (type === 'lead') return renderLead(data);
    else if (type === 'visit' || type === 'followup') return renderVisit(data);
    else if (type === 'proposal') return renderProposal(data);
    else if (type === 'transaksi') return renderTransaksi(data);
}

function renderLead(d) {
    return `
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <div style="background:linear-gradient(135deg,#3b82f6,#2563eb);padding:1rem;border-radius:0.5rem;margin:-0.25rem -0.25rem 0;">
        <span style="font-size:0.65rem;color:rgba(255,255,255,0.85);font-weight:600;text-transform:uppercase;display:block;margin-bottom:0.25rem;letter-spacing:0.5px;"><i class="fas fa-building"></i> Lead - Perusahaan</span>
        <div style="font-size:1.1rem;font-weight:700;color:#fff;line-height:1.3;">${h(d.company)}</div>
      </div>
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user"></i> PIC</span>
          <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.pic_name)}</div>
          <small style="color:#6b7280;display:block;font-size:0.75rem;margin-top:0.4rem;"><i class="fas fa-phone" style="width:14px;"></i> ${h(d.pic_phone)}</small>
        </div>
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user-tie"></i> Sales</span>
          <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.sales_name)}</div>
          <small style="color:#6b7280;display:block;font-size:0.75rem;margin-top:0.4rem;"><i class="fas fa-envelope" style="width:14px;"></i> ${h(d.sales_email)}</small>
        </div>
      </div>
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
        <div>
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-calendar"></i> Tanggal Visit</span>
          <div style="font-size:0.95rem;font-weight:600;color:#111827;margin-top:0.3rem;">${h(d.visit_date)}</div>
        </div>
        <div>
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-map-marker-alt"></i> Lokasi</span>
          <div style="font-size:0.85rem;color:#111827;margin-top:0.3rem;line-height:1.4;">${h(d.location)}</div>
        </div>
      </div>
      
      <div>
        <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-lightbulb"></i> Purpose</span>
        <div style="background:#eff6ff;border-left:3px solid #3b82f6;padding:0.75rem;border-radius:0.375rem;font-size:0.85rem;color:#1e40af;line-height:1.5;margin-top:0.3rem;">${h(d.visit_purpose)}</div>
      </div>
      
      <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:0.625rem;margin:0 -1.5rem -1.5rem;border-radius:0 0 0.5rem 0.5rem;">
        <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-clock"></i> ${h(d.created_at)}</small>
      </div>
    </div>`;
}

function renderVisit(d) {
    return `
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <div style="background:linear-gradient(135deg,#10b981,#059669);padding:1rem;border-radius:0.5rem;margin:-0.25rem -0.25rem 0;">
        <span style="font-size:0.65rem;color:rgba(255,255,255,0.85);font-weight:600;text-transform:uppercase;display:block;margin-bottom:0.25rem;letter-spacing:0.5px;"><i class="fas fa-building"></i> Perusahaan</span>
        <div style="font-size:1.1rem;font-weight:700;color:#fff;line-height:1.3;">${h(d.company)}</div>
      </div>
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user"></i> PIC</span>
          <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.pic_name)}</div>
          <div style="display:flex;flex-direction:column;gap:0.25rem;margin-top:0.4rem;">
            <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-briefcase" style="width:14px;"></i> ${h(d.pic_position)}</small>
            <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-phone" style="width:14px;"></i> ${h(d.pic_phone)}</small>
            <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-envelope" style="width:14px;"></i> ${h(d.pic_email)}</small>
          </div>
        </div>
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user-tie"></i> Sales</span>
          <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.sales_name)}</div>
          <small style="color:#6b7280;display:block;font-size:0.75rem;margin-top:0.4rem;"><i class="fas fa-envelope" style="width:14px;"></i> ${h(d.sales_email)}</small>
        </div>
      </div>
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
        <div>
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-calendar"></i> Tanggal Visit</span>
          <div style="font-size:0.95rem;font-weight:600;color:#111827;margin-top:0.3rem;">${h(d.visit_date)}</div>
        </div>
        <div>
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-redo"></i> Follow Up</span>
          <span class="badge-status ${d.is_follow_up === 'Ya' ? 'badge-success' : 'badge-info'}" style="margin-top:0.3rem;display:inline-block;">${h(d.is_follow_up)}</span>
        </div>
      </div>
      
      <div>
        <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-map-marker-alt"></i> Lokasi</span>
        <div style="font-size:0.85rem;color:#111827;line-height:1.4;margin-top:0.3rem;">
          ${h(d.location)}
          ${d.address && d.address !== '-' ? `<br><small style="color:#6b7280;font-size:0.75rem;margin-top:0.15rem;display:inline-block;">${h(d.address)}</small>` : ''}
        </div>
      </div>
    <div>
    <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-lightbulb"></i> Purpose</span>
    <div style="background:#eff6ff;border-left:3px solid #3b82f6;padding:0.75rem;border-radius:0.375rem;font-size:0.85rem;color:#1e40af;line-height:1.5;margin-top:0.3rem;">${h(d.visit_purpose)}</div>
  </div>
  
  <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:0.625rem;margin:0 -1.5rem -1.5rem;border-radius:0 0 0.5rem 0.5rem;">
    <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-clock"></i> Dibuat: ${h(d.created_at)}</small>
  </div>
</div>`;

}
function renderProposal(d) {
const methodIcons = {
'Email': 'fas fa-envelope',
'WhatsApp': 'fab fa-whatsapp',
'Door to Door': 'fas fa-door-open',
'Courier': 'fas fa-shipping-fast',
'Fax': 'fas fa-fax'
};

const statusColors = {
    'Menunggu Review': 'badge-info',
    'Dalam Pembahasan': 'badge-status',
    'Menunggu Approval': 'badge-success',
    'Ditolak': 'badge-danger',
    'Disetujui': 'badge-success'
};

return `
<div style="display:flex;flex-direction:column;gap:1rem;">
  <div style="background:linear-gradient(135deg,#ec4899,#db2777);padding:1rem;border-radius:0.5rem;margin:-0.25rem -0.25rem 0;">
    <span style="font-size:0.65rem;color:rgba(255,255,255,0.85);font-weight:600;text-transform:uppercase;display:block;margin-bottom:0.25rem;letter-spacing:0.5px;"><i class="fas fa-file-alt"></i> Pengajuan Proposal</span>
    <div style="font-size:1.1rem;font-weight:700;color:#fff;line-height:1.3;">${h(d.company)}</div>
  </div>
  
  <div style="background:linear-gradient(135deg,#fce7f3,#fbcfe8);border-left:4px solid #ec4899;padding:0.875rem;border-radius:0.5rem;">
    <div style="display:grid;grid-template-columns:1fr auto;gap:0.75rem;align-items:start;">
      <div>
        <span class="detail-label" style="color:#831843;font-size:0.65rem;"><i class="fas fa-file-signature"></i> Judul Proposal</span>
        <div style="font-size:1rem;font-weight:700;color:#9f1239;margin-top:0.3rem;line-height:1.3;">${h(d.proposal_title)}</div>
        <small style="color:#831843;display:block;margin-top:0.4rem;font-size:0.75rem;"><i class="fas fa-hashtag" style="width:14px;"></i> ${h(d.proposal_number)}</small>
      </div>
      <div style="text-align:right;">
        <span class="detail-label" style="color:#831843;font-size:0.65rem;"><i class="fas fa-money-bill-wave"></i> Nilai</span>
        <div style="font-size:1.1rem;font-weight:700;color:#9f1239;margin-top:0.3rem;">${h(d.proposal_value)}</div>
      </div>
    </div>
  </div>
  
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
      <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user"></i> PIC</span>
      <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.pic_name)}</div>
      <div style="display:flex;flex-direction:column;gap:0.25rem;margin-top:0.4rem;">
        <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-briefcase" style="width:14px;"></i> ${h(d.pic_position)}</small>
        <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-phone" style="width:14px;"></i> ${h(d.pic_phone)}</small>
        <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-envelope" style="width:14px;"></i> ${h(d.pic_email)}</small>
      </div>
    </div>
    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
      <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user-tie"></i> Sales</span>
      <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.sales_name)}</div>
      <small style="color:#6b7280;display:block;font-size:0.75rem;margin-top:0.4rem;"><i class="fas fa-envelope" style="width:14px;"></i> ${h(d.sales_email)}</small>
    </div>
  </div>
  
  <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:0.75rem;align-items:end;">
    <div>
      <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-calendar"></i> Tanggal Pengajuan</span>
      <div style="font-size:0.95rem;font-weight:600;color:#111827;margin-top:0.3rem;">${h(d.proposal_date)}</div>
    </div>
    <div>
      <span class="detail-label" style="font-size:0.65rem;"><i class="${methodIcons[d.submission_method] || 'fas fa-paper-plane'}"></i> Metode Pengiriman</span>
      <div style="font-size:0.95rem;font-weight:600;color:#111827;margin-top:0.3rem;">${h(d.submission_method)}</div>
    </div>
    <div>
      <span class="badge-status ${statusColors[d.status] || 'badge-info'}" style="font-size:0.75rem;padding:0.4rem 0.8rem;">
        ${h(d.status)}
      </span>
    </div>
  </div>
  
  ${d.notes && d.notes !== '-' ? `
  <div>
    <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-sticky-note"></i> Catatan</span>
    <div style="background:#fef3c7;border-left:3px solid #f59e0b;padding:0.75rem;border-radius:0.375rem;font-size:0.85rem;color:#92400e;line-height:1.5;margin-top:0.3rem;">${h(d.notes)}</div>
  </div>
  ` : ''}
  
  <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:0.625rem;margin:0 -1.5rem -1.5rem;border-radius:0 0 0.5rem 0.5rem;">
    <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-clock"></i> Dibuat: ${h(d.created_at)}</small>
  </div>
</div>`;
}

function renderTransaksi(d) {
    return `
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:1rem;border-radius:0.5rem;margin:-0.25rem -0.25rem 0;">
        <span style="font-size:0.65rem;color:rgba(255,255,255,0.85);font-weight:600;text-transform:uppercase;display:block;margin-bottom:0.25rem;letter-spacing:0.5px;"><i class="fas fa-file-invoice-dollar"></i> Transaksi</span>
        <div style="font-size:1.1rem;font-weight:700;color:#fff;line-height:1.3;">${h(d.nama_perusahaan)}</div>
      </div>
      
      <div style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);border-left:4px solid #10b981;padding:0.875rem;border-radius:0.5rem;text-align:center;">
        <span class="detail-label" style="color:#065f46;font-size:0.65rem;"><i class="fas fa-money-bill-wave"></i> Nilai Proyek</span>
        <div style="font-size:1.4rem;font-weight:700;color:#047857;margin-top:0.3rem;">${h(d.nilai_proyek)}</div>
      </div>
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user"></i> PIC</span>
          <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.pic_name)}</div>
          <div style="display:flex;flex-direction:column;gap:0.25rem;margin-top:0.4rem;">
            <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-phone" style="width:14px;"></i> ${h(d.pic_phone)}</small>
            <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-envelope" style="width:14px;"></i> ${h(d.pic_email)}</small>
          </div>
        </div>
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:0.875rem;">
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-user-tie"></i> Sales</span>
          <div style="font-weight:600;font-size:0.95rem;color:#111827;margin-top:0.3rem;">${h(d.nama_sales)}</div>
          <small style="color:#6b7280;display:block;font-size:0.75rem;margin-top:0.4rem;"><i class="fas fa-envelope" style="width:14px;"></i> ${h(d.sales_email)}</small>
        </div>
      </div>
      
      <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:0.75rem;align-items:end;">
        <div>
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-calendar-check"></i> Mulai Kerja</span>
          <div style="font-size:0.9rem;font-weight:600;color:#111827;margin-top:0.3rem;">${h(d.tanggal_mulai_kerja)}</div>
        </div>
        <div>
          <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-calendar-times"></i> Selesai Kerja</span>
          <div style="font-size:0.9rem;font-weight:600;color:#111827;margin-top:0.3rem;">${h(d.tanggal_selesai_kerja)}</div>
        </div>
        <div>
          <span class="badge-status ${d.status === 'Deals' ? 'badge-success' : 'badge-danger'}" style="font-size:0.75rem;padding:0.4rem 0.8rem;">
            ${h(d.status)}
          </span>
        </div>
      </div>
      
      ${d.work_duration && d.work_duration !== '-' ? `
      <div>
        <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-hourglass-half"></i> Total Durasi</span>
        <div style="font-size:1rem;font-weight:700;color:#b45309;margin-top:0.3rem;">${h(d.work_duration)}</div>
      </div>
      ` : ''}
      
      ${d.keterangan && d.keterangan !== '-' ? `
      <div>
        <span class="detail-label" style="font-size:0.65rem;"><i class="fas fa-sticky-note"></i> Keterangan</span>
        <div style="background:#f0fdf4;border-left:3px solid #10b981;padding:0.75rem;border-radius:0.375rem;font-size:0.85rem;color:#065f46;line-height:1.5;margin-top:0.3rem;">${h(d.keterangan)}</div>
      </div>
      ` : ''}
      
      <div style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:0.625rem;margin:0 -1.5rem -1.5rem;border-radius:0 0 0.5rem 0.5rem;">
        <small style="color:#6b7280;font-size:0.75rem;"><i class="fas fa-clock"></i> Dibuat: ${h(d.created_at)}</small>
      </div>
    </div>`;
}

function showError(msg) {
    return `
    <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.875rem;">
        <i class="fas fa-exclamation-circle" style="color: #dc2626; font-size: 2rem;"></i>
        <div>
          <h5 style="margin: 0 0 0.25rem 0; font-weight: 600; color: #7f1d1d; font-size: 0.95rem;">Gagal Memuat Data</h5>
          <p style="margin: 0; color: #991b1b; font-size: 0.85rem; line-height: 1.4;">${h(msg)}</p>
        </div>
      </div>
    </div>`;
}

function h(t) {
    if (!t || t === 'null' || t === 'undefined') return '-';
    const m = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
    return String(t).replace(/[&<>"']/g, c => m[c]);
}
</script>

@endsection