<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = "Pessoas";
    protected static ?string $navigationLabel = "Clientes";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxValue(50),
                    TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord:true),
                    TextInput::make('phone')
                        ->label('Telefone')
                        ->maxValue(50),
                    DatePicker::make('dob')
                        ->label('Aniversário'),
                    TextInput::make('city')
                        ->label('Cidade')
                        ->required()
                        ->maxValue(50),
                    TextInput::make('state')
                        ->label('Província')
                        ->maxValue(50),
                    TextInput::make('address')
                        ->label('Endereço')
                        ->maxValue(50),
                    TextInput::make('zip_code')
                        ->label('Código Zip')
                        ->maxValue(50)
                        ->columnSpan('full'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label('Cidade')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('dob')
                    ->date("d/m/Y")
                    ->label('Aniversário')
                    ->sortable(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
