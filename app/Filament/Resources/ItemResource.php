<?php

namespace App\Filament\Resources;

use App\Enums\ProductTypeEnum;
use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use League\CommonMark\Input\MarkdownInput;
use Illuminate\Support\Str;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = "Items e Inventário";
    protected static ?string $navigationLabel = "Items";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make("name")
                            ->label("Nome")
                            ->live(onBlur: true)
                            ->unique(Item::class, 'name', ignoreRecord:true)
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
                            ->unique(Item::class, 'slug', ignoreRecord:true),
                        MarkdownEditor::make("description")
                            ->label("Descrição")->columnSpan('full')
                    ])->columns(2),

                    Section::make('Preço e Inventário')->schema([
                        TextInput::make("sku")
                            ->label("SKU (Unidade de Manutenção de Estoque)")
                            ->unique(Item::class, 'sku', ignoreRecord:true)
                            ->required(),
                        TextInput::make("price")
                            ->label('Preço')
                            ->rules('regex:/^\d{1,6}(\.\d{0,2})?$/')
                            ->required(),
                        TextInput::make("quantity")
                            ->label('Quantidade')
                            // ->rules(['integer', 'min:0'])
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000),
                        Select::make('type')->options([
                            'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                            'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                            'product' => ProductTypeEnum::PRODUCT->value,
                            'service' => ProductTypeEnum::SERVICE->value,
                        ])->required()
                    ])->columns(2)

                ]),

                Group::make()->schema([
                    Section::make('Status')->schema([
                        Toggle::make('is_visible')
                            ->label("Visivel")
                            ->helperText('Ativar ou desativar a visibilidade do item')
                            ->default(true),
                        Toggle::make('is_featured')
                        ->helperText('Ativar ou desativar o destaque do item')
                        ->label("Destaque"),
                        DatePicker::make('published_at')
                        ->default(now())
                        ->label("Data de publicação")
                    ]),

                    Section::make('Image')->schema([
                        FileUpload::make('image')
                        ->directory('form-atachments')
                        ->preserveFilenames()
                        ->imageEditor()
                        ->previewable()
                    ])->collapsible(),

                    Section::make('Associations')->schema([
                        Select::make('brand_id')->relationship(name:'brand', titleAttribute:'name')
                    ])
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make("image")->label("Imagem"),
                TextColumn::make("sku"),
                TextColumn::make("name")
                ->searchable()
                ->sortable()
                ->label("Nome"),
                TextColumn::make('brand.name')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->label("Marca"),
                IconColumn::make('is_visible')->boolean()
                    ->sortable()
                    ->toggleable()
                    ->label("Visivel"),
                TextColumn::make('price')
                    ->sortable()
                    ->toggleable()
                    ->label('Preço'),
                TextColumn::make('quantity')
                    ->sortable()
                    ->toggleable()
                    ->label("Quantidade"),
                TextColumn::make('published_at')
                    ->date("d/m/Y")
                    ->sortable()
                    ->toggleable()
                    ->label("Data de publicação"),
                TextColumn::make("type")->label("Tipo")
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label('Visibilidade')
                    ->boolean()
                    ->trueLabel('Apenas items visiveis')
                    ->falseLabel('Apenas items invisiveis')
                    ->native(false),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
