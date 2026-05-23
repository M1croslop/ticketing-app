<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Synapso — {{ $generatedAt }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #fff;
            padding: 32px 40px;
        }

        /* ── Cabecera ── */
        .report-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 28px;
        }
        .report-logo { display: flex; align-items: center; gap: 10px; }
        .report-logo-icon {
            width: 36px; height: 36px; background: #F59E0B;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }
        .report-logo-icon svg { width: 18px; height: 18px; }
        .report-logo-name { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
        .report-meta { text-align: right; color: #64748b; font-size: 11px; line-height: 1.7; }
        .report-meta strong { color: #1e293b; }

        /* ── Secciones ── */
        .section { margin-bottom: 32px; break-inside: avoid; }
        .section-title {
            font-size: 13px; font-weight: 700; color: #0f172a;
            text-transform: uppercase; letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px; margin-bottom: 14px;
            display: flex; align-items: center; gap: 6px;
        }
        .section-title::before {
            content: ''; display: inline-block;
            width: 3px; height: 14px; background: #F59E0B; border-radius: 2px;
        }

        /* ── KPI Grid ── */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .kpi-card {
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 10px; padding: 14px 16px;
        }
        .kpi-label { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; }
        .kpi-value { font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 4px; line-height: 1; }
        .kpi-value span { font-size: 15px; font-weight: 600; color: #64748b; }
        .kpi-sub { font-size: 9px; color: #94a3b8; margin-top: 6px; font-weight: 500; }

        /* ── SLA bar ── */
        .sla-bar-wrap { background: #e2e8f0; border-radius: 4px; height: 5px; margin-top: 8px; overflow: hidden; }
        .sla-bar { height: 100%; border-radius: 4px; }
        .sla-green { background: #10b981; }
        .sla-amber { background: #f59e0b; }
        .sla-red   { background: #ef4444; }

        /* ── Tablas ── */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead tr { background: #f8fafc; }
        th {
            padding: 8px 12px; text-align: left;
            font-size: 9px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.06em;
            border-bottom: 1px solid #e2e8f0;
        }
        td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tbody tr:nth-child(even) td { background: #fafafa; }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center; padding: 2px 8px;
            border-radius: 4px; font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;
        }
        .badge-red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-orange { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
        .badge-amber  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-blue   { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-green  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-slate  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* ── Barra de categoría ── */
        .cat-bar { display: flex; height: 6px; border-radius: 3px; overflow: hidden; background: #e2e8f0; }
        .cat-bar-open    { background: #60a5fa; }
        .cat-bar-prog    { background: #fbbf24; }
        .cat-bar-res     { background: #34d399; }

        /* ── Estado vencido urgencia ── */
        .overdue-72 { color: #dc2626; font-weight: 800; }
        .overdue-24 { color: #ea580c; font-weight: 700; }
        .overdue-ok { color: #d97706; font-weight: 700; }

        /* ── Avatar ── */
        .avatar {
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 24px; border-radius: 50%;
            background: #F59E0B; color: #fff;
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            margin-right: 6px; flex-shrink: 0;
        }

        /* ── Pie ── */
        .report-footer {
            margin-top: 40px; padding-top: 16px; border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between;
            color: #94a3b8; font-size: 10px;
        }

        /* ── Print ── */
        @media print {
            body { padding: 20px 24px; }
            .no-print { display: none !important; }
            .section { break-inside: avoid; }
        }
    </style>
</head>
<body>

    {{-- ── Botón imprimir (solo pantalla) ── --}}
    <div class="no-print" style="position:fixed;top:16px;right:16px;z-index:100;display:flex;gap:8px;">
        <button onclick="window.print()"
                style="background:#F59E0B;color:#fff;border:none;border-radius:8px;
                       padding:8px 18px;font-size:12px;font-weight:700;cursor:pointer;
                       box-shadow:0 2px 8px rgba(0,0,0,.15);">
            🖨 Imprimir / Guardar PDF
        </button>
        <button onclick="window.close()"
                style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;
                       padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer;">
            Cerrar
        </button>
    </div>

    {{-- ── Cabecera del reporte ── --}}
    <div class="report-header">
        <div class="report-logo">
            <div class="report-logo-icon">
                <svg fill="white" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="report-logo-name">Synapso</span>
        </div>
        <div class="report-meta">
            <div><strong>Reporte de Métricas y Operaciones</strong></div>
            <div>Generado: {{ $generatedAt }}</div>
            <div>Por: {{ $generatedBy }}</div>
            @php
                $sectionNames = [
                    'kpis'       => 'KPIs generales',
                    'categories' => 'Categorías',
                    'overdue'    => 'Vencidos',
                    'agents'     => 'Agentes',
                    'users'      => 'Usuarios',
                ];
            @endphp
            <div style="margin-top:4px;">
                Secciones: {{ implode(', ', array_map(fn($s) => $sectionNames[$s] ?? $s, $sections)) }}
            </div>
        </div>
    </div>

    {{--SECCIÓN: KPIs--}}
    @if(in_array('kpis', $sections))
        <div class="section">
            <div class="section-title">KPIs Generales del Sistema</div>
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Total Tickets</div>
                    <div class="kpi-value">{{ number_format($totalTickets) }}</div>
                    <div class="kpi-sub">{{ $openCount }} abiertos · {{ $resolvedCount }} resueltos</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Tiempo Prom. Resolución</div>
                    <div class="kpi-value">
                        {{ $avgResolutionTime ?? '—' }}
                        @if($avgResolutionTime)<span>hrs</span>@endif
                    </div>
                    <div class="kpi-sub">Desde creación hasta resolución</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">En Progreso</div>
                    <div class="kpi-value">{{ $inProgressCount }}</div>
                    <div class="kpi-sub">Tickets actualmente en trabajo</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Cumplimiento SLA</div>
                    <div class="kpi-value" style="color:{{ $slaComplianceRate >= 80 ? '#16a34a' : ($slaComplianceRate >= 60 ? '#d97706' : '#dc2626') }}">
                        {{ $slaComplianceRate }}<span>%</span>
                    </div>
                    <div class="sla-bar-wrap">
                        <div class="sla-bar {{ $slaComplianceRate >= 80 ? 'sla-green' : ($slaComplianceRate >= 60 ? 'sla-amber' : 'sla-red') }}"
                             style="width:{{ $slaComplianceRate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SECCIÓN: CATEGORÍAS--}}
    @if(in_array('categories', $sections) && isset($ticketsByCategory))
        <div class="section">
            <div class="section-title">Tickets por Categoría</div>
            <table>
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Distribución</th>
                        <th style="text-align:center">Abiertos</th>
                        <th style="text-align:center">En Progreso</th>
                        <th style="text-align:center">Resueltos</th>
                        <th style="text-align:right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticketsByCategory as $cat)
                        @php
                            $openW = $cat->tickets_count > 0 ? round(($cat->open_count        / $cat->tickets_count) * 100) : 0;
                            $inPW  = $cat->tickets_count > 0 ? round(($cat->in_progress_count / $cat->tickets_count) * 100) : 0;
                            $resW  = $cat->tickets_count > 0 ? round(($cat->resolved_count    / $cat->tickets_count) * 100) : 0;
                        @endphp
                        <tr>
                            <td style="font-weight:600;color:#0f172a">{{ $cat->name }}</td>
                            <td style="width:140px">
                                <div class="cat-bar">
                                    @if($openW > 0)<div class="cat-bar-open" style="width:{{ $openW }}%"></div>@endif
                                    @if($inPW  > 0)<div class="cat-bar-prog" style="width:{{ $inPW }}%"></div>@endif
                                    @if($resW  > 0)<div class="cat-bar-res"  style="width:{{ $resW }}%"></div>@endif
                                </div>
                            </td>
                            <td style="text-align:center">{{ $cat->open_count }}</td>
                            <td style="text-align:center">{{ $cat->in_progress_count }}</td>
                            <td style="text-align:center">{{ $cat->resolved_count }}</td>
                            <td style="text-align:right;font-weight:700;color:#0f172a">{{ $cat->tickets_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- SECCIÓN: VENCIDOS--}}
    @if(in_array('overdue', $sections) && isset($overdueTickets))
        <div class="section">
            <div class="section-title">
                Tickets Vencidos
                @if($overdueTickets->count() > 0)
                    <span class="badge badge-red" style="margin-left:8px">{{ $overdueTickets->count() }} activos</span>
                @endif
            </div>
            @if($overdueTickets->isEmpty())
                <p style="color:#64748b;font-style:italic;padding:12px 0">
                    ✓ Todos los tickets se encuentran dentro del SLA.
                </p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Prioridad</th>
                            <th>Categoría</th>
                            <th>Agente</th>
                            <th>Venció</th>
                            <th>Vencido hace</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueTickets as $ticket)
                            @php
                                $priorityBadge = [
                                    'urgent' => 'badge-red',
                                    'high'   => 'badge-orange',
                                    'medium' => 'badge-amber',
                                    'low'    => 'badge-blue',
                                ];
                                $priorityLabel = ['urgent' => 'Crítico', 'high' => 'Alta', 'medium' => 'Media', 'low' => 'Baja'];
                                $hoursOver = (int) abs(now()->diffInHours($ticket->due_date));
                                $overLabel = $hoursOver >= 24 ? round($hoursOver / 24, 1).'d' : $hoursOver.'h';
                                $urgencyCls = $hoursOver >= 72 ? 'overdue-72' : ($hoursOver >= 24 ? 'overdue-24' : 'overdue-ok');
                            @endphp
                            <tr>
                                <td style="font-family:monospace;font-size:10px;color:#64748b">#{{ sprintf('%04d', $ticket->id) }}</td>
                                <td style="font-weight:600;color:#0f172a;max-width:200px">{{ $ticket->title }}</td>
                                <td>
                                    <span class="badge {{ $priorityBadge[$ticket->priority] ?? 'badge-slate' }}">
                                        {{ $priorityLabel[$ticket->priority] ?? $ticket->priority }}
                                    </span>
                                </td>
                                <td>{{ $ticket->category->name ?? '—' }}</td>
                                <td>{{ $ticket->agent->name ?? 'Sin asignar' }}</td>
                                <td style="color:#64748b">{{ $ticket->due_date->format('d/m/Y H:i') }}</td>
                                <td><span class="{{ $urgencyCls }}">{{ $overLabel }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    {{-- SECCIÓN: AGENTES--}}
    @if(in_array('agents', $sections) && isset($topAgents))
        <div class="section">
            <div class="section-title">Rendimiento de Agentes</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Agente</th>
                        <th style="text-align:center">Tickets Activos</th>
                        <th style="text-align:center">Tickets Resueltos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topAgents as $i => $agent)
                        <tr>
                            <td style="font-weight:700;color:{{ $i === 0 ? '#d97706' : '#94a3b8' }}">
                                #{{ $i + 1 }}
                            </td>
                            <td>
                                <div style="display:flex;align-items:center">
                                    <span class="avatar">{{ strtoupper(substr($agent->name, 0, 2)) }}</span>
                                    <div>
                                        <div style="font-weight:600;color:#0f172a">{{ $agent->name }}</div>
                                        <div style="font-size:10px;color:#94a3b8">{{ $agent->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center;font-weight:600">{{ $agent->active_count }}</td>
                            <td style="text-align:center;font-weight:700;color:#16a34a">{{ $agent->resolved_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- SECCIÓN: USUARIOS--}}
    @if(in_array('users', $sections) && isset($allUsers))
        <div class="section">
            <div class="section-title">Directorio de Usuarios</div>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th style="text-align:center">Tickets Creados</th>
                        <th style="text-align:center">Tickets Asignados</th>
                        <th>Registrado</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allUsers as $u)
                        @php
                            $roleBadge = ['admin' => 'badge-amber', 'agent' => 'badge-green', 'client' => 'badge-slate'];
                            $roleLabel = ['admin' => 'Admin', 'agent' => 'Agente', 'client' => 'Cliente'];
                        @endphp
                        <tr>
                            <td style="font-weight:600;color:#0f172a">{{ $u->name }}</td>
                            <td style="color:#64748b">{{ $u->email }}</td>
                            <td>
                                <span class="badge {{ $roleBadge[$u->role] ?? 'badge-slate' }}">
                                    {{ $roleLabel[$u->role] ?? $u->role }}
                                </span>
                            </td>
                            <td style="text-align:center">{{ $u->tickets_count }}</td>
                            <td style="text-align:center">{{ $u->assigned_tickets_count }}</td>
                            <td style="color:#64748b">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($u->deleted_at)
                                    <span class="badge badge-red">Suspendido</span>
                                @else
                                    <span class="badge badge-green">Activo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ── Pie del reporte ── --}}
    <div class="report-footer">
        <span>Synapso — Sistema de Gestión de Tickets</span>
        <span>Generado el {{ $generatedAt }} por {{ $generatedBy }}</span>
    </div>

    <script>
        // Auto-trigger print dialog al cargar
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 600);
        });
    </script>

</body>
</html>