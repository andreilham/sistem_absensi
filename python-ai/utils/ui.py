"""
Komponen UI Streamlit — tampilan sama dengan Laravel (biru UMB).
"""

import streamlit as st

# CSS global agar Streamlit terlihat seperti halaman Laravel
LARAVEL_CSS = """
<style>
    /* Sembunyikan menu default Streamlit */
    #MainMenu {visibility: hidden;}
    footer {visibility: hidden;}
    header[data-testid="stHeader"] {background: transparent;}

    /* Background gradient seperti Laravel */
    .stApp {
        background: linear-gradient(to bottom, #eff6ff, #ffffff);
    }

    /* Header custom */
    .umb-header {
        background: linear-gradient(90deg, #ffffff 0%, #eff6ff 100%);
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.12);
        padding: 16px 24px;
        margin: -1rem -1rem 1.5rem -1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .umb-logo {
        width: 48px; height: 48px;
        background: #2563eb;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 22px;
    }
    .umb-title { font-weight: 700; color: #1f2937; margin: 0; font-size: 1.1rem; }
    .umb-subtitle { color: #6b7280; margin: 0; font-size: 0.85rem; }
    .umb-clock { font-size: 1.5rem; font-weight: 700; color: #1d4ed8; font-family: monospace; }
    .umb-date { color: #6b7280; font-size: 0.85rem; text-align: right; }

    /* Kartu putih */
    .umb-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        padding: 24px;
        margin-bottom: 16px;
    }

    /* Badge jadwal */
    .umb-jadwal {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 12px 16px;
        color: #1e40af;
        text-align: center;
        margin-bottom: 16px;
    }

    /* Sidebar biru seperti admin Laravel */
    section[data-testid="stSidebar"] {
        background: linear-gradient(180deg, #1e3a8a 0%, #1d4ed8 100%) !important;
    }
    section[data-testid="stSidebar"] * {
        color: #dbeafe !important;
    }
    section[data-testid="stSidebar"] .stRadio label {
        color: #dbeafe !important;
    }
    section[data-testid="stSidebar"] .stButton>button {
        background: #ffffff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
</style>
"""


def apply_laravel_theme():
    """Terapkan CSS tema Laravel ke halaman Streamlit."""
    st.markdown(LARAVEL_CSS, unsafe_allow_html=True)


def render_header():
    """Header sama seperti halaman Laravel."""
    apply_laravel_theme()
    st.markdown("""
    <div class="umb-header">
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="umb-logo">🎓</div>
            <div>
                <p class="umb-title">Universitas Maju Bersama</p>
                <p class="umb-subtitle">Sistem Cerdas — Python + Streamlit</p>
            </div>
        </div>
        <div>
            <p class="umb-clock" id="umb-clock">--:--:--</p>
            <p class="umb-date" id="umb-date">-</p>
        </div>
    </div>
    <script>
        function updateClock() {
            const now = new Date();
            const clock = document.getElementById('umb-clock');
            const date = document.getElementById('umb-date');
            if (clock) clock.textContent = now.toLocaleTimeString('id-ID');
            if (date) date.textContent = now.toLocaleDateString('id-ID', {
                weekday:'long', year:'numeric', month:'long', day:'numeric'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    """, unsafe_allow_html=True)


def render_jadwal_info(jadwal: dict | None):
    """Tampilkan banner jadwal aktif."""
    if jadwal:
        st.markdown(f"""
        <div class="umb-jadwal">
            📚 Jadwal aktif: <strong>{jadwal.get('mata_kuliah', '-')}</strong>
            ({jadwal.get('kelas', '-')})
            · {str(jadwal.get('jam_mulai', ''))[:5]} - {str(jadwal.get('jam_selesai', ''))[:5]}
        </div>
        """, unsafe_allow_html=True)
    else:
        st.warning("Tidak ada jadwal kuliah aktif saat ini.")
