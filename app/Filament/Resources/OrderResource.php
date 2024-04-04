<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = "Items e Inventário";
    protected static ?string $navigationLabel = "Pedidos";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Order Details')
                        ->schema([
                            TextInput::make('number')
                                ->default('OR-' . random_int(100000,99999999))
                                ->disabled()
                                ->required()
                                ->dehydrated()
                                ->required(),
                            Select::make('customer_id')
                                ->relationship('customer', 'name')
                                ->searchable()
                                ->required(),
                            Select::make('status')->options([
                                'pending' => OrderStatusEnum::PENDING->value,
                                'processing' => OrderStatusEnum::PROCESSING->value,
                                'completed' => OrderStatusEnum::COMPLETED->value,
                                'declined' => OrderStatusEnum::DECLINED->value,
                            ])->columnSpanFull(),
                            MarkdownEditor::make('notes')->columnSpanFull()
                        ])->columns(2),
                    Step::make('Order Items')
                        ->schema([

                        ]),
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("number")
                    ->searchable()
                    ->sortable()
                    ->label("Número"),
                TextColumn::make("customer.name")
                    ->searchable()
                    ->sortable()
                    ->label("Cliente"),
                TextColumn::make("status")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("total_price")
                    ->searchable()
                    ->sortable()
                    ->summarize([
                        Sum::make()->money()
                    ]),
                TextColumn::make("created_at")
                    ->date("d/m/Y H:i:s")
                    ->label("Data do pedido"),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
