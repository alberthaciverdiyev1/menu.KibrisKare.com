<x-filament-panels::page>
    <style>
        .qr-card-root {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .qr-gradient-badge {
            background: linear-gradient(135deg, #E85D3F 0%, #C9472F 100%);
        }
        .qr-card-container {
            background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF7 100%);
            border: 1px solid #EAE5DC;
            border-radius: 28px;
            box-shadow: 0 10px 30px -5px rgba(44, 24, 16, 0.08), 0 4px 12px -2px rgba(44, 24, 16, 0.04);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .qr-card-container:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -8px rgba(232, 93, 63, 0.15), 0 8px 16px -4px rgba(44, 24, 16, 0.06);
            border-color: #E85D3F55;
        }
        .qr-code-box {
            background: #FFFFFF;
            padding: 16px;
            border-radius: 22px;
            border: 2px dashed #E5DFD5;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .qr-center-badge {
            position: absolute;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 2.5px solid #E85D3F;
            box-shadow: 0 4px 12px rgba(232, 93, 63, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 2px;
        }
        .qr-center-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>

    <div class="qr-card-root space-y-6">

        @if(!$restaurant)
            <div style="padding: 32px; text-align: center; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 20px; color: #92400E; font-size: 14px; font-weight: 600;">
                Lütfen önce restoran profilinizi kaydedin.
            </div>
        @elseif($branches->isEmpty())
            <div style="padding: 48px 24px; text-align: center; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: #F3F4F6; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    🏢
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #1F2937; margin-bottom: 8px;">Henüz Şube Eklenmemiş</h3>
                <p style="font-size: 13px; color: #6B7280; max-width: 420px; margin: 0 auto 20px;">
                    Şubelere özel QR menüler oluşturabilmek için önce Şubeler sekmesinden en az bir şube eklemelisiniz.
                </p>
                <a href="{{ route('filament.restaurant.resources.branches.create') }}" 
                   style="display: inline-flex; align-items: center; gap: 8px; background: #E85D3F; color: #FFFFFF; padding: 10px 24px; border-radius: 14px; font-size: 13px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(232,93,63,0.3);">
                    + Yeni Şube Ekle
                </a>
            </div>
        @else
            <!-- Grid of Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px;">
                @foreach($branches as $branch)
                    @php
                        $menuUrl = route('restaurant.menu', ['restaurant' => $restaurant->slug, 'branch' => $branch->id]);
                        // High quality crisp dark terracotta QR code
                        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&margin=8&color=2C1810&bgcolor=FFFFFF&data=" . urlencode($menuUrl);
                        $cardId = "branch-qr-card-" . $branch->id;
                    @endphp

                    <div style="display: flex; flex-direction: column; gap: 14px;">

                        <!-- PHYSICAL TABLE STAND CARD -->
                        <div id="{{ $cardId }}" class="qr-card-container" style="padding: 28px 24px; text-align: center;">
                            
                            <!-- Top Colored Strip -->
                            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #E85D3F, #F4A261, #E85D3F);"></div>

                            <!-- Header: Logo & Restaurant Title -->
                            <div style="margin-bottom: 20px;">
                                <div style="width: 56px; height: 56px; margin: 0 auto 12px; border-radius: 18px; background: #FFFFFF; border: 1.5px solid #E8E3DC; box-shadow: 0 4px 10px rgba(0,0,0,0.06); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                    @if($restaurant->image)
                                        <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <span style="font-size: 24px;">🍽️</span>
                                    @endif
                                </div>

                                <h3 style="font-size: 19px; font-weight: 900; color: #2C1810; margin: 0; letter-spacing: -0.5px; text-transform: uppercase;">
                                    {{ $restaurant->name }}
                                </h3>

                                <div style="display: inline-flex; align-items: center; gap: 6px; margin-top: 4px; padding: 3px 10px; background: #FAF0ED; border-radius: 9999px;">
                                    <span style="font-size: 12px; font-weight: 800; color: #E85D3F;">
                                        📍 {{ $branch->name }}
                                    </span>
                                    @if($branch->is_main)
                                        <span style="font-size: 9px; font-weight: 800; text-transform: uppercase; background: #E85D3F; color: #FFFFFF; padding: 1px 6px; border-radius: 6px;">Merkez</span>
                                    @endif
                                </div>

                                <div style="font-size: 11px; color: #8C827A; margin-top: 4px; font-weight: 500;">
                                    {{ $branch->city->name ?? 'Kuzey Kıbrıs' }} • {{ $restaurant->cuisine }}
                                </div>
                            </div>

                            <!-- Center: QR Code with Framing and Watermark -->
                            <div style="margin: 20px 0;">
                                <div class="qr-code-box">
                                    <img src="{{ $qrCodeUrl }}" 
                                         alt="{{ $branch->name }} QR Menü" 
                                         style="width: 190px; height: 190px; display: block; border-radius: 12px;">

                                    <div class="qr-center-badge">
                                        @if($restaurant->image)
                                            <img src="{{ Str::startsWith($restaurant->image, ['http://', 'https://']) ? $restaurant->image : asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}">
                                        @else
                                            <span style="font-size: 18px;">🍽️</span>
                                        @endif
                                    </div>
                                </div>

                                <div style="margin-top: 14px;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; color: #2C1810; letter-spacing: 0.3px;">
                                        <span>📷</span>
                                        <span>KAMERANIZLA OKUTUN</span>
                                    </div>
                                    <div style="font-size: 11px; color: #8C827A; margin-top: 2px;">
                                        Temassız Dijital Menü & Fiyat Listesi
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Action Buttons -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <a href="{{ $menuUrl }}" 
                               target="_blank" 
                               style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 14px; background: #FFFFFF; color: #374151; border: 1px solid #D1D5DB; border-radius: 12px; font-size: 12px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.2s;">
                                <span>📱</span>
                                <span>Menüyü Aç</span>
                            </a>

                            <button type="button" 
                                    onclick="printStandCard('{{ $cardId }}', '{{ addslashes($restaurant->name) }} - {{ addslashes($branch->name) }}')"
                                    style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 14px; background: #E85D3F; color: #FFFFFF; border: none; border-radius: 12px; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(232, 93, 63, 0.25); transition: all 0.2s;">
                                <span>🖨️</span>
                                <span>Yazdır / İndir</span>
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <!-- Stand Card Print Engine -->
    <script>
        function printStandCard(elementId, title) {
            const card = document.getElementById(elementId);
            if (!card) return;

            const printWindow = window.open('', '_blank', 'width=800,height=900');
            const cardHtml = card.outerHTML;

            const doc = printWindow.document;
            doc.open();
            doc.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>' + title + ' Masa Kartı</title>' +
                '<style>' +
                '@page { size: A5 portrait; margin: 10mm; }' +
                'body { margin: 0; padding: 20px; background: #F4F0E8; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }' +
                '.print-wrapper { width: 320px; margin: auto; }' +
                '@media print { body { background: white !important; padding: 0 !important; min-height: auto !important; } .print-wrapper { width: 320px !important; } }' +
                '</style></head><body>' +
                '<div class="print-wrapper">' + cardHtml + '</div>' +
                '</body></html>');
            doc.close();

            printWindow.focus();
            setTimeout(function() {
                printWindow.print();
            }, 500);
        }
    </script>
</x-filament-panels::page>
