<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $restaurant->name }} - {{ $branch->name }} Masa Stant Kartı</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 10mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            margin: 0;
            padding: 30px 15px;
            background: #FAF8F5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #2C1810;
        }
        .print-controls {
            position: fixed;
            top: 20px;
            display: flex;
            gap: 12px;
            z-index: 100;
        }
        .btn-print {
            background: #E85D3F;
            color: #FFFFFF;
            padding: 10px 24px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 14px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(232, 93, 63, 0.35);
        }
        .btn-close {
            background: #FFFFFF;
            color: #4B5563;
            padding: 10px 20px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #D1D5DB;
            cursor: pointer;
        }
        .card-container {
            width: 320px;
            background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF7 100%);
            border: 1.5px solid #EAE5DC;
            border-radius: 28px;
            box-shadow: 0 12px 36px rgba(44, 24, 16, 0.08);
            padding: 32px 24px;
            text-align: center;
            position: relative;
            margin-top: 40px;
        }
        .restaurant-title {
            font-size: 20px;
            font-weight: 900;
            color: #2C1810;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .branch-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #FAF0ED;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 800;
            color: #E85D3F;
        }
        .meta-subtitle {
            font-size: 11px;
            color: #8C827A;
            margin-top: 6px;
            font-weight: 500;
        }
        .qr-box {
            background: #FFFFFF;
            padding: 16px;
            border-radius: 22px;
            border: 2px dashed #E5DFD5;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 22px 0 16px 0;
        }
        .qr-image {
            width: 200px;
            height: 200px;
            display: block;
            border-radius: 12px;
        }
        .qr-badge {
            position: absolute;
            width: 50px;
            height: 50px;
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
        .qr-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .scan-heading {
            font-size: 12px;
            font-weight: 800;
            color: #2C1810;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .scan-subtext {
            font-size: 11px;
            color: #8C827A;
            margin-top: 3px;
        }
        @media print {
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                min-height: auto !important;
            }
            .print-controls {
                display: none !important;
            }
            .card-container {
                margin: 0 auto !important;
                box-shadow: none !important;
                border: 1px solid #DCD5CB !important;
            }
        }
    </style>
</head>
<body>

    <div class="print-controls">
        <button class="btn-print" onclick="window.print()">🖨️ Yazdır (Print)</button>
        <button class="btn-close" onclick="window.close()">Kapat</button>
    </div>

    <div class="card-container">
        <div>
            <h1 class="restaurant-title">{{ $restaurant->name }}</h1>
            <div class="branch-badge">
                📍 {{ $branch->name }}
            </div>
            <div class="meta-subtitle">
                {{ $branch->city->name ?? 'Kuzey Kıbrıs' }} • {{ $restaurant->cuisine }}
            </div>
        </div>

        <div class="qr-box">
            <img src="{{ $qrCodeUrl }}" alt="{{ $branch->name }} QR Menü" class="qr-image">
            <div class="qr-badge">
                @if($restaurant->image)
                    <img src="{{ Str::startsWith($restaurant->image, ['http://', 'https://']) ? $restaurant->image : asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}">
                @else
                    <span style="font-size: 20px;">🍽️</span>
                @endif
            </div>
        </div>

        <div>
            <div class="scan-heading">
                <span>📷</span>
                <span>KAMERANIZLA OKUTUN</span>
            </div>
            <div class="scan-subtext">
                Temassız Dijital Menü & Fiyat Listesi
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
