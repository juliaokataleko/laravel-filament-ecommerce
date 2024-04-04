<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-in';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationGroup = "Items e Inventário";
    protected static ?string $navigationLabel = "Marcas";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make("name")
                            ->label("Nome")
                            ->live(onBlur: true)
                            ->unique(Brand::class, 'name', ignoreRecord: true)
                            ->required()
                            ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make("slug")
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Brand::class, 'slug', ignoreRecord: true),
                        TextInput::make('url')->label('Url')
                            ->unique(Brand::class, 'url', ignoreRecord: true)
                            ->columnSpan('full'),
                        MarkdownEditor::make('description')
                            ->columnSpan('full')
                            ->label('Descrição')
                    ])->columns(2)
                ]),
                Group::make()->schema([
                    Section::make('Status')->schema([
                        Toggle::make('is_visible')->label('Visibilidade')
                            ->helperText('Ativar ou desativar a visibilidade')
                    ]),
                    Section::make('Cor')->schema([
                        ColorPicker::make('primary_hex')->label('Cor')
                    ])
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
                TextColumn::make('url')->label('URL')->searchable()->sortable(),
                ColorColumn::make('primary_hex')->label('Cor'),
                IconColumn::make('is_visible')->boolean()
                    ->sortable()
                    ->toggleable()
                    ->label("Visivel"),
                TextColumn::make('updated_at')->date()->label('Data de atualização')
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
