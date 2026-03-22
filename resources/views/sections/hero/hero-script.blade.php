@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==================== DATA TOOLS ====================
    const toolsData = [
        { id: 1, name: 'Camera', status: 'Available' },
        { id: 2, name: 'Tripod', status: 'Borrowed' },
        { id: 3, name: 'Microphone', status: 'Borrowed' },
        { id: 4, name: 'Lighting', status: 'Available' },
        { id: 5, name: 'Lens', status: 'Available' }
    ];

    // Warna border untuk nomor berdasarkan ID
    const borderColors = [
        'border-purple-950',
        'border-purple-950',
        'border-purple-950',
        'border-purple-700',
        'border-purple-600'
    ];

    // Background pattern untuk row (selang-seling)
    const bgPatterns = ['bg-white/100', 'bg-white/90'];

    const tableBody = document.getElementById('toolsTableBody');
    
    // ==================== FUNGSI RENDER TABEL NORMAL ====================
    function renderTable(order) {
        // Urutkan: Available di atas Borrowed
        const sortedOrder = [...order].sort((a, b) => {
            if (a.status === 'Available' && b.status !== 'Available') return -1;
            if (a.status !== 'Available' && b.status === 'Available') return 1;
            return 0;
        });

        let html = '';
        
        sortedOrder.forEach((tool, index) => {
            const rowNumber = index + 1;
            const bgClass = bgPatterns[index % 2];
            const borderColor = borderColors[tool.id - 1] || 'border-purple-950';
            const statusClass = tool.status === 'Available' ? 'status-available' : 'status-borrowed';
            
            html += `
                <tr class="tools-row ${bgClass}" data-id="${tool.id}" data-status="${tool.status}" style="transition: all 0.5s ease; opacity: 1;">
                    <td class="px-3 py-1">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border-2 ${borderColor} text-[0.5rem] font-extrabold">
                            ${rowNumber}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-semibold">${tool.name}</td>
                    <td class="px-5 py-3">
                        <span class="${statusClass}">${tool.status}</span>
                    </td>
                </tr>
            `;
        });
        
        tableBody.innerHTML = html;
    }

    // ==================== FUNGSI ANIMASI SWIPE DOWN ====================
    function animateTableUpdate(changedTools, changeType) {
        // Urutkan data sesuai status
        const sortedOrder = [...toolsData].sort((a, b) => {
            if (a.status === 'Available' && b.status !== 'Available') return -1;
            if (a.status !== 'Available' && b.status === 'Available') return 1;
            return 0;
        });

        // Dapatkan posisi baru untuk tool yang berubah
        const newPositions = {};
        sortedOrder.forEach((tool, index) => {
            if (changedTools.some(t => t.id === tool.id)) {
                newPositions[tool.id] = index + 1;
            }
        });

        console.log('Tool yang berubah:', changedTools.map(t => `${t.name}: ${t.oldStatus} → ${t.newStatus} -> Row ${newPositions[t.id]}`));

        // Sembunyikan semua row dengan fade out
        const rows = document.querySelectorAll('#toolsTableBody tr');
        rows.forEach(row => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(-20px)';
        });

        // Setelah fade out, render ulang dengan animasi masuk
        setTimeout(() => {
            // Render tabel baru
            renderTable(toolsData);
            
            // Sembunyikan semua row baru dulu
            const newRows = document.querySelectorAll('#toolsTableBody tr');
            newRows.forEach(row => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(-30px)';
            });
            
            // Force reflow
            void tableBody.offsetHeight;
            
            // Animasi masuk untuk semua row
            newRows.forEach((row, index) => {
                const toolId = row.getAttribute('data-id');
                const changedTool = changedTools.find(t => t.id == toolId);
                
                setTimeout(() => {
                    if (changedTool && changedTool.newStatus === 'Available') {
                        // Row yang berubah ke Available: animasi swipe down menonjol
                        row.style.transition = 'all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)';
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                        row.style.backgroundColor = '#f0fdf4';
                        row.style.boxShadow = '0 4px 12px rgba(99, 51, 255, 0.3)';
                        
                        // Highlight status badge
                        const statusSpan = row.querySelector('td:last-child span');
                        if (statusSpan) {
                            statusSpan.style.transition = 'all 0.5s ease';
                            statusSpan.style.transform = 'scale(1.2)';
                            statusSpan.style.color = '#059669';
                            statusSpan.style.fontWeight = 'bold';
                            
                            setTimeout(() => {
                                statusSpan.style.transform = 'scale(1)';
                            }, 300);
                        }
                        
                        // Kembalikan background setelah animasi
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                            row.style.boxShadow = '';
                        }, 1000);
                    } else if (changedTool && changedTool.newStatus === 'Borrowed') {
                        // Row yang berubah ke Borrowed: animasi berbeda (sedikit merah)
                        row.style.transition = 'all 0.6s ease';
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                        row.style.backgroundColor = '#fef2f2';
                        
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                        }, 800);
                    } else {
                        // Row biasa: animasi normal
                        row.style.transition = 'all 0.5s ease';
                        row.style.opacity = '1';
                        row.style.transform = 'translateY(0)';
                    }
                }, index * 100); // Stagger animation
            });
        }, 300);
    }

    // ==================== FUNGSI UPDATE STATUS RANDOM ====================
    function updateRandomStatus() {
        // Tentukan jumlah perubahan (1 atau 2 tool)
        const numberOfChanges = Math.random() > 0.6 ? 2 : 1;
        const changedTools = [];
        const availableTools = toolsData.filter(t => t.status === 'Available');
        const borrowedTools = toolsData.filter(t => t.status === 'Borrowed');
        
        for (let i = 0; i < numberOfChanges; i++) {
            // Random pilih tipe perubahan: Available ↔ Borrowed
            if (Math.random() > 0.5 && borrowedTools.length > 0) {
                // Ubah dari Borrowed ke Available
                const randomIndex = Math.floor(Math.random() * borrowedTools.length);
                const toolToChange = borrowedTools[randomIndex];
                const oldStatus = toolToChange.status;
                
                toolToChange.status = 'Available';
                
                changedTools.push({
                    id: toolToChange.id,
                    name: toolToChange.name,
                    oldStatus: oldStatus,
                    newStatus: 'Available'
                });
                
                // Update arrays
                borrowedTools.splice(randomIndex, 1);
                availableTools.push(toolToChange);
                
            } else if (availableTools.length > 0) {
                // Ubah dari Available ke Borrowed
                const randomIndex = Math.floor(Math.random() * availableTools.length);
                const toolToChange = availableTools[randomIndex];
                const oldStatus = toolToChange.status;
                
                toolToChange.status = 'Borrowed';
                
                changedTools.push({
                    id: toolToChange.id,
                    name: toolToChange.name,
                    oldStatus: oldStatus,
                    newStatus: 'Borrowed'
                });
                
                // Update arrays
                availableTools.splice(randomIndex, 1);
                borrowedTools.push(toolToChange);
            }
        }
        
        if (changedTools.length > 0) {
            console.log(`${changedTools.length} tool(s) berubah status:`, changedTools);
            animateTableUpdate(changedTools);
        }
    }

    // ==================== ANIMASI CAT & DINO ====================
    // ==================== ANIMASI CAT & DINO ====================
const catImages = [
    '{{ asset('images/cat.svg') }}',
    '{{ asset('images/cat-pose-2.svg') }}',
    '{{ asset('images/cat-pose-3.svg') }}'
];

const dinoImages = [
    '{{ asset('images/dino.svg') }}',
    '{{ asset('images/dino-pose-2.svg') }}',
    '{{ asset('images/dino-pose-3.svg') }}'
];

let currentCatIndex = 0;
let currentDinoIndex = 0;
let lastCatIndex = 0;
let lastDinoIndex = 0;
const catElement = document.getElementById('catImage');
const dinoElement = document.getElementById('dinoImage');

// Fungsi untuk mendapatkan index random yang berbeda dari index sebelumnya
function getRandomIndex(max, lastIndex) {
    let newIndex;
    do {
        newIndex = Math.floor(Math.random() * max);
    } while (newIndex === lastIndex && max > 1);
    return newIndex;
}

// Fungsi animasi cat dengan random pose dan efek goyang
function animateCat() {
    // Simpan index sebelumnya
    lastCatIndex = currentCatIndex;
    
    // Dapatkan index random yang berbeda dari sebelumnya
    currentCatIndex = getRandomIndex(catImages.length, lastCatIndex);
    
    // Ganti gambar
    catElement.src = catImages[currentCatIndex];
    
    // Efek goyang kecil
    catElement.style.transition = 'transform 0.2s ease-in-out';
    catElement.style.transform = 'rotate(3deg)';
    
    setTimeout(() => {
        catElement.style.transform = 'rotate(-3deg)';
    }, 100);
    
    setTimeout(() => {
        catElement.style.transform = 'rotate(2deg)';
    }, 200);
    
    setTimeout(() => {
        catElement.style.transform = 'rotate(-2deg)';
    }, 300);
    
    setTimeout(() => {
        catElement.style.transform = 'rotate(0deg)';
        // Hapus transition setelah selesai
        setTimeout(() => {
            catElement.style.transition = '';
        }, 50);
    }, 400);
}

// Fungsi animasi dino dengan random pose dan efek goyang
function animateDino() {
    // Simpan index sebelumnya
    lastDinoIndex = currentDinoIndex;
    
    // Dapatkan index random yang berbeda dari sebelumnya
    currentDinoIndex = getRandomIndex(dinoImages.length, lastDinoIndex);
    
    // Ganti gambar
    dinoElement.src = dinoImages[currentDinoIndex];
    
    // Efek goyang kecil
    dinoElement.style.transition = 'transform 0.2s ease-in-out';
    dinoElement.style.transform = 'rotate(4deg)';
    
    setTimeout(() => {
        dinoElement.style.transform = 'rotate(-4deg)';
    }, 120);
    
    setTimeout(() => {
        dinoElement.style.transform = 'rotate(3deg)';
    }, 240);
    
    setTimeout(() => {
        dinoElement.style.transform = 'rotate(-3deg)';
    }, 360);
    
    setTimeout(() => {
        dinoElement.style.transform = 'rotate(0deg)';
        // Hapus transition setelah selesai
        setTimeout(() => {
            dinoElement.style.transition = '';
        }, 50);
    }, 480);
}
    // ==================== STACK SVG ANIMATION ====================
    const stack = document.getElementById('svgStack');
    
    // Data gambar
    const images = [
        { id: 1, src: '{{ asset('images/logsinfo-1.svg') }}', alt: 'Log 1' },
        { id: 2, src: '{{ asset('images/logsinfo-2.svg') }}', alt: 'Log 2' },
        { id: 3, src: '{{ asset('images/logsinfo-3.svg') }}', alt: 'Log 3' },
        { id: 4, src: '{{ asset('images/logsinfo-4.svg') }}', alt: 'Log 4' }
    ];
    
    // Urutan yang diinginkan: [1,2,3] -> [2,3,4] -> [3,4,1] -> [4,1,2] -> kembali ke [1,2,3]
    const sequences = [
        [1, 2, 3],
        [2, 3, 4],
        [3, 4, 1],
        [4, 1, 2]
    ];
    
    let currentSeqIndex = 0;
    let isAnimating = false;
    let animationTimeout;
    
    // Hitung jarak antar item (dalam px)
    const GAP = 8;
    
    // Buat elemen DOM untuk setiap gambar
    function createItems() {
        images.forEach(img => {
            const div = document.createElement('div');
            div.className = 'stack-item';
            div.setAttribute('data-id', img.id);
            div.setAttribute('data-visible', 'false');
            
            const imgElement = document.createElement('img');
            imgElement.src = img.src;
            imgElement.alt = img.alt;
            
            div.appendChild(imgElement);
            stack.appendChild(div);
        });
    }
    
    // Fungsi untuk mengatur posisi item berdasarkan sequence
    function positionItems(seqIndex, animate = true) {
        const items = document.querySelectorAll('.stack-item');
        const sequence = sequences[seqIndex];
        
        items.forEach(item => {
            if (!animate) {
                item.style.transition = 'none';
            }
            item.style.opacity = '0';
            item.style.bottom = '0';
            item.style.zIndex = '0';
        });
        
        if (!animate) {
            void stack.offsetHeight;
        }
        
        let bottomPosition = 0;
        let heights = {};
        
        items.forEach(item => {
            const id = item.getAttribute('data-id');
            heights[id] = item.offsetHeight;
        });
        
        for (let i = 2; i >= 0; i--) {
            const itemId = sequence[i];
            const item = Array.from(items).find(el => el.getAttribute('data-id') == itemId);
            
            if (item) {
                item.style.opacity = '1';
                item.style.zIndex = (i + 1).toString();
                
                if (i === 2) {
                    bottomPosition = 0;
                } else if (i === 1) {
                    const bottomId = sequence[2];
                    bottomPosition = (heights[bottomId] || 0) + GAP;
                } else if (i === 0) {
                    const bottomId = sequence[2];
                    const middleId = sequence[1];
                    bottomPosition = (heights[bottomId] || 0) + GAP + (heights[middleId] || 0) + GAP;
                }
                
                item.style.bottom = bottomPosition + 'px';
            }
        }
        
        if (!animate) {
            setTimeout(() => {
                items.forEach(item => {
                    item.style.transition = '';
                });
            }, 50);
        }
    }
 
    
    // Fungsi untuk melakukan animasi ke sequence berikutnya
    function animateToNextSequence() {
        if (isAnimating) return;
        
        isAnimating = true;
        
        const nextSeqIndex = (currentSeqIndex + 1) % sequences.length;
        
        const items = document.querySelectorAll('.stack-item');
        const currentSequence = sequences[currentSeqIndex];
        const enteringItemId = sequences[nextSeqIndex][2];
        
        const exitingItemId = currentSequence[0];
        const exitingItem = Array.from(items).find(el => el.getAttribute('data-id') == exitingItemId);
        
        const enteringItem = Array.from(items).find(el => el.getAttribute('data-id') == enteringItemId);
        
        if (exitingItem) {
            exitingItem.style.transition = 'all 0.8s cubic-bezier(0.25, 0.1, 0.15, 1.05)';
            exitingItem.style.opacity = '0';
            exitingItem.style.transform = 'translateY(-30px)';
            exitingItem.style.zIndex = '0';
        }
        
        if (enteringItem) {
            enteringItem.style.transition = 'all 0.8s cubic-bezier(0.25, 0.1, 0.15, 1.05)';
            enteringItem.style.opacity = '0';
            enteringItem.style.bottom = '-50px';
            enteringItem.style.zIndex = '1';
        }
        
        setTimeout(() => {
            if (exitingItem) {
                exitingItem.style.transform = '';
            }
            
            const heights = {};
            items.forEach(item => {
                const id = item.getAttribute('data-id');
                heights[id] = item.offsetHeight;
            });
            
            const bottomId = sequences[nextSeqIndex][2];
            const middleId = sequences[nextSeqIndex][1];
            const topId = sequences[nextSeqIndex][0];
            
            const bottomItem = Array.from(items).find(el => el.getAttribute('data-id') == bottomId);
            if (bottomItem) {
                bottomItem.style.transition = 'all 0.8s cubic-bezier(0.25, 0.1, 0.15, 1.05)';
                bottomItem.style.opacity = '1';
                bottomItem.style.bottom = '0';
                bottomItem.style.zIndex = '1';
            }
            
            setTimeout(() => {
                const middleItem = Array.from(items).find(el => el.getAttribute('data-id') == middleId);
                if (middleItem) {
                    const bottomHeight = heights[bottomId] || 0;
                    middleItem.style.transition = 'all 0.8s cubic-bezier(0.25, 0.1, 0.15, 1.05)';
                    middleItem.style.opacity = '1';
                    middleItem.style.bottom = (bottomHeight + GAP) + 'px';
                    middleItem.style.zIndex = '2';
                }
                
                setTimeout(() => {
                    const topItem = Array.from(items).find(el => el.getAttribute('data-id') == topId);
                    if (topItem) {
                        const bottomHeight = heights[bottomId] || 0;
                        const middleHeight = heights[middleId] || 0;
                        topItem.style.transition = 'all 0.8s cubic-bezier(0.25, 0.1, 0.15, 1.05)';
                        topItem.style.opacity = '1';
                        topItem.style.bottom = (bottomHeight + GAP + middleHeight + GAP) + 'px';
                        topItem.style.zIndex = '3';
                    }
                    
                    currentSeqIndex = nextSeqIndex;
                    
                    setTimeout(() => {
                        isAnimating = false;
                        
                        if (animationTimeout) {
                            clearTimeout(animationTimeout);
                        }
                        animationTimeout = setTimeout(animateToNextSequence, 2500);
                    }, 200);
                }, 100);
            }, 100);
        }, 400);
    }
    
    // ==================== INISIALISASI ====================
    // ==================== ANIMASI MILESTONE ====================
function initMilestoneAnimation() {
    const milestoneElement = document.querySelector('.bg-white.rounded-xl span:first-child');
    const percentElement = document.querySelector('.bg-white.rounded-xl span:nth-child(2)');
    
    if (!milestoneElement || !percentElement) return;
    
    // Total hours awal = 67 jam
    const totalHours = 67;
    
    // Milestone: [persen, sisa jam]
    const milestones = [
        { percent: 0, hours: 67 },
        { percent: 21, hours: 53 },
        { percent: 44, hours: 38 },
        { percent: 67, hours: 22 },
        { percent: 100, hours: 0 }
    ];
    
    let currentIndex = 0;
    let isAnimating = false;
    
    // Buat elemen progress bar di background
    const container = document.querySelector('.bg-white.rounded-xl');
    container.style.position = 'relative';
    container.style.overflow = 'hidden';
    container.style.border = '2px solid #000000';
    container.style.backgroundColor = '#ffffff'; // Pastikan background putih solid
    container.style.opacity = '1'; // Pastikan tidak transparan
    
    const progressBar = document.createElement('div');
    progressBar.className = 'milestone-progress';
    progressBar.style.position = 'absolute';
    progressBar.style.bottom = '0';
    progressBar.style.left = '0';
    progressBar.style.height = '100%';
    progressBar.style.width = '0%';
    progressBar.style.background = '#FAAE2B';
    progressBar.style.transition = 'width 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
    progressBar.style.opacity = '0.15';
    progressBar.style.zIndex = '1';
    progressBar.style.borderRadius = '0';
    
    container.appendChild(progressBar);
    
    // Pastikan teks berada di atas progress bar
    const textElements = container.querySelectorAll('span');
    textElements.forEach(span => {
        span.style.position = 'relative';
        span.style.zIndex = '2';
    });
    
    // Fungsi untuk mengupdate progress bar
    function updateProgressBar(percent) {
        progressBar.style.width = percent + '%';
        
        // Efek opacity berbeda untuk setiap milestone
        if (percent === 100) {
            progressBar.style.opacity = '1';
            progressBar.style.background = '#FAAE2B';
        } else if (percent >= 67) {
            progressBar.style.opacity = '1';
            progressBar.style.background = '#FAAE2B';
        } else if (percent >= 44) {
            progressBar.style.opacity = '1';
            progressBar.style.background = '#FAAE2B';
        } else if (percent >= 21) {
            progressBar.style.opacity = '1';
            progressBar.style.background = '#FAAE2B';
        } else {
            progressBar.style.opacity = '1';
            progressBar.style.background = '#FAAE2B';
        }
    }
    
    // Fungsi untuk mengupdate tampilan dengan animasi
    function updateMilestone(targetMilestone) {
        if (isAnimating) return;
        isAnimating = true;
        
        // Ambil nilai saat ini
        const currentText = milestoneElement.textContent;
        const currentHours = parseInt(currentText) || 67;
        const currentPercent = parseInt(percentElement.textContent) || 0;
        
        const targetHours = targetMilestone.hours;
        const targetPercent = targetMilestone.percent;
        
        const startTime = performance.now();
        const duration = 800; // 800ms untuk animasi angka
        
        function animateValue(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function untuk efek lebih smooth
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            
            // Hitung nilai intermediate
            const hoursDelta = targetHours - currentHours;
            const percentDelta = targetPercent - currentPercent;
            
            const intermediateHours = Math.round(currentHours + (hoursDelta * easeOutCubic));
            const intermediatePercent = Math.round(currentPercent + (percentDelta * easeOutCubic));
            
            // Update tampilan
            milestoneElement.textContent = intermediateHours + ' Hours Remaining';
            percentElement.textContent = intermediatePercent + '%';
            
            // Update progress bar secara real-time di background
            progressBar.style.width = intermediatePercent + '%';
            
            if (progress < 1) {
                requestAnimationFrame(animateValue);
            } else {
                // Pastikan nilai akhir tepat
                milestoneElement.textContent = targetHours + ' Hours Remaining';
                percentElement.textContent = targetPercent + '%';
                updateProgressBar(targetPercent);
                isAnimating = false;
                
                // Efek khusus saat mencapai 0 jam (100%)
                if (targetHours === 0) {
                    setTimeout(() => {
                        milestoneElement.textContent = '0 Hours Remaining';
                    }, 100);
                }
            }
        }
        
        requestAnimationFrame(animateValue);
    }
    
    // Fungsi untuk efek pulse container
    function pulseEffect(milestone) {
        const container = document.querySelector('.bg-white.rounded-xl');
        
        // Efek berbeda berdasarkan persentase
        if (milestone.percent === 100) {
            // Efek spesial untuk 100% (0 jam)
            container.style.transform = 'scale(1.08)';
            container.style.transition = 'transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
            container.style.boxShadow = '0 0 50px rgba(153, 102, 255, 0.5)';
            container.style.backgroundColor = 'rgba(153, 102, 255, 0.1)';
            
            // Animasi untuk teks
            milestoneElement.style.animation = 'numberPop 0.5s ease';
            percentElement.style.animation = 'numberPop 0.5s ease';
            
            setTimeout(() => {
                container.style.transform = 'scale(1)';
            }, 500);
            
            setTimeout(() => {
                container.style.boxShadow = '';
                container.style.backgroundColor = '';
                milestoneElement.style.animation = '';
                percentElement.style.animation = '';
            }, 1000);
            
        } else if (milestone.percent === 67) {
            container.style.transform = 'scale(1.04)';
            container.style.transition = 'transform 0.3s ease';
            container.style.boxShadow = '0 0 30px rgba(153, 102, 255, 0.3)';
            
            setTimeout(() => {
                container.style.transform = 'scale(1)';
            }, 300);
            
            setTimeout(() => {
                container.style.boxShadow = '';
            }, 600);
            
        } else if (milestone.percent === 44) {
            container.style.transform = 'scale(1.03)';
            container.style.transition = 'transform 0.25s ease';
            container.style.boxShadow = '0 0 25px rgba(153, 102, 255, 0.25)';
            
            setTimeout(() => {
                container.style.transform = 'scale(1)';
            }, 250);
            
            setTimeout(() => {
                container.style.boxShadow = '';
            }, 500);
            
        } else if (milestone.percent === 21) {
            container.style.transform = 'scale(1.02)';
            container.style.transition = 'transform 0.2s ease';
            container.style.boxShadow = '0 0 20px rgba(153, 102, 255, 0.2)';
            
            setTimeout(() => {
                container.style.transform = 'scale(1)';
            }, 200);
            
            setTimeout(() => {
                container.style.boxShadow = '';
            }, 400);
            
        } else if (milestone.percent === 0) {
            // Efek reset ke 67 jam
            container.style.transform = 'scale(0.98)';
            container.style.transition = 'transform 0.3s ease';
            
            setTimeout(() => {
                container.style.transform = 'scale(1)';
            }, 300);
        }
    }
    
    // Fungsi untuk memulai animasi milestone
    function startMilestoneAnimation() {
        // Ambil milestone berikutnya
        currentIndex = (currentIndex + 1) % milestones.length;
        const nextMilestone = milestones[currentIndex];
        
        // Efek pulse sebelum animasi
        pulseEffect(nextMilestone);
        
        // Update angka dengan animasi
        updateMilestone(nextMilestone);
        
        // Jadwalkan animasi berikutnya
        let nextInterval;
        
        if (nextMilestone.percent === 100) {
            nextInterval = 5000; // Lebih lama untuk 100% (0 jam)
        } else if (nextMilestone.percent === 67) {
            nextInterval = 4000;
        } else if (nextMilestone.percent === 44) {
            nextInterval = 3500;
        } else if (nextMilestone.percent === 21) {
            nextInterval = 3000;
        } else {
            nextInterval = 3000;
        }
        
        setTimeout(startMilestoneAnimation, nextInterval);
    }
    
    // Set initial values
    milestoneElement.textContent = '67 Hours Remaining';
    percentElement.textContent = '0%';
    updateProgressBar(0);
    
    // Mulai animasi setelah delay 2 detik
    setTimeout(startMilestoneAnimation, 2000);
}
    // ==================== INISIALISASI ====================
function init() {
    // Render tabel pertama kali
    renderTable(toolsData);
    
    // Set interval untuk update status random setiap 4 detik
    setInterval(updateRandomStatus, 4000);
    
    // Set interval untuk animasi cat setiap 3 detik dengan random pose
    setInterval(animateCat, 3000);
    
    // Set interval untuk animasi dino setiap 3 detik
    setTimeout(() => {
        setInterval(animateDino, 3000);
    }, 1500);
    
    // Inisialisasi stack SVG
    createItems();
    
    setTimeout(() => {
        positionItems(0, false);
        currentSeqIndex = 0;
        
        setTimeout(() => {
            animateToNextSequence();
        }, 2000);
    }, 100);
    
    // Inisialisasi animasi milestone
    initMilestoneAnimation();
    
    window.addEventListener('resize', () => {
        positionItems(currentSeqIndex, false);
    });
}
    
    init();
});
</script>
@endpush