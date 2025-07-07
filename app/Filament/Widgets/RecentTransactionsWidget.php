<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Transactions';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 2;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->with(['user'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Reference copied!')
                    ->limit(20),
                    
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->limit(25)
                    ->default('N/A'),
                    
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit_purchase' => 'success',
                        'withdrawal' => 'warning',
                        'transfer' => 'info',
                        'refund' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state))),
                    
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}