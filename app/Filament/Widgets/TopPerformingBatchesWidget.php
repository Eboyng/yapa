<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Batch;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopPerformingBatchesWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Performing Batches';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 2;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Batch::query()
                    ->withCount('members')
                    ->where('status', 'active')
                    ->orderByDesc('members_count')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Batch Name')
                    ->searchable()
                    ->limit(30)
                    ->weight('bold'),
                    
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'trial' => 'info',
                        'regular' => 'success',
                        'premium' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    
                TextColumn::make('members_count')
                    ->label('Members')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                    
                TextColumn::make('max_members')
                    ->label('Capacity')
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->members_count . '/' . $state
                    ),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('members_count', 'desc')
            ->paginated(false);
    }
}