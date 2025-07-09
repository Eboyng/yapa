<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Audit Logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Audit logs are read-only, no form needed
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('adminUser.name')
                    ->label('Admin User')
                    ->sortable()
                    ->searchable()
                    ->default('System'),
                
                Tables\Columns\TextColumn::make('targetUser.name')
                    ->label('Target User')
                    ->sortable()
                    ->searchable()
                    ->default('N/A'),
                
                Tables\Columns\BadgeColumn::make('action')
                    ->label('Action')
                    ->colors([
                        'primary' => ['user_created', 'user_updated', 'batch_created', 'batch_updated'],
                        'success' => ['user_approved', 'ad_approved', 'transaction_approved'],
                        'warning' => ['user_flagged', 'user_banned', 'batch_closed'],
                        'danger' => ['user_deleted', 'ad_rejected', 'transaction_rejected'],
                        'info' => ['login', 'logout', 'password_changed'],
                    ])
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->default('N/A'),
                
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'user_created' => 'User Created',
                        'user_updated' => 'User Updated',
                        'user_approved' => 'User Approved',
                        'user_flagged' => 'User Flagged',
                        'user_banned' => 'User Banned',
                        'user_deleted' => 'User Deleted',
                        'batch_created' => 'Batch Created',
                        'batch_updated' => 'Batch Updated',
                        'batch_closed' => 'Batch Closed',
                        'ad_created' => 'Ad Created',
                        'ad_updated' => 'Ad Updated',
                        'ad_approved' => 'Ad Approved',
                        'ad_rejected' => 'Ad Rejected',
                        'transaction_created' => 'Transaction Created',
                        'transaction_updated' => 'Transaction Updated',
                        'transaction_approved' => 'Transaction Approved',
                        'transaction_rejected' => 'Transaction Rejected',
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'password_changed' => 'Password Changed',
                    ])
                    ->multiple(),
                
                SelectFilter::make('admin_user_id')
                    ->label('Admin User')
                    ->relationship('adminUser', 'name')
                    ->searchable()
                    ->preload(),
                
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('From Date'),
                        DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for audit logs
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Audit logs are auto-generated
    }

    public static function canEdit($record): bool
    {
        return false; // Audit logs are read-only
    }

    public static function canDelete($record): bool
    {
        return false; // Audit logs should not be deleted
    }

    public static function canDeleteAny(): bool
    {
        return false; // Audit logs should not be deleted
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}