<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\ContactMessage;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $newMessagesCount = ContactMessage::where('is_read', '0')->count();
        $productsCount = Product::count();
        $pendingOrdersCount = Order::where('shipping_status', 'Pendiente')->count();
        $processingOrdersCount = Order::where('shipping_status', 'en proceso')->count();

        // Configurar zona horaria
        Carbon::setLocale('es');
        $timezone = 'America/Mexico_City';
        date_default_timezone_set($timezone);
        
        // Obtener fechas de la semana actual
        $now = Carbon::now($timezone);
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SATURDAY);

// Obtener ventas (solo pagadas)
$weekSales = Order::where('status', 'paid')  // Agregar este filtro
    ->whereBetween('created_at', [
        $startOfWeek->copy()->startOfDay(),
        $endOfWeek->copy()->endOfDay()
    ])
    ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
    ->groupBy('date')
    ->orderBy('date')
    ->get();

$totalWeekSales = $weekSales->sum('total');


    // Obtener solo los meses donde hay ventas pagadas
    $salesMonths = Order::where('status', 'paid')
        ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get()
        ->map(function ($item) {
            $date = Carbon::create($item->year, $item->month, 1);
            $date->locale('es'); // Establecer locale a español
            return [
                'year' => $item->year,
                'month' => $item->month,
                'label' => ucfirst($date->monthName) . ' ' . $item->year // Mes en español
            ];
        });

    // Obtener ventas del mes actual (solo pagadas)
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;
    
    $monthSales = Order::where('status', 'paid')
        ->whereYear('created_at', $currentYear)
        ->whereMonth('created_at', $currentMonth)
        ->sum('total_price');

        return Inertia::render('Admin/Dashboard', [
            'usersCount' => $usersCount,
            'newMessagesCount' => $newMessagesCount,
            'productsCount' => $productsCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'processingOrdersCount' => $processingOrdersCount,
            'weekSales' => $weekSales,
            'totalWeekSales' => $totalWeekSales,
            'weekRange' => [
                'start' => $startOfWeek->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
            ],
            'salesMonths' => $salesMonths,
            'totalMonthSales' => $monthSales,
            'currentPeriod' => [
            'year' => $currentYear,
            'month' => $currentMonth
            ],
        ]);
    }

    public function getMonthlySales(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);
    
        try {
            $totalSales = Order::where('status', 'paid')
                ->whereYear('created_at', $validated['year'])
                ->whereMonth('created_at', $validated['month'])
                ->sum('total_price');
    
            return response()->json([
                'total' => $totalSales,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las ventas',
                'success' => false
            ], 500);
        }
    }
}