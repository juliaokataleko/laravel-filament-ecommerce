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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use League\CommonMark\Input\MarkdownInput;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = "Shop";
    protected static ?string $navigationLabel = "Items";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make("name")->label("Nome")->required(),
                        TextInput::make("slug")->required(),
                        MarkdownEditor::make("description")
                            ->label("Descrição")->columnSpan('full')
                    ])->columns(2),

                    Section::make('Preço e Inventário')->schema([
                        TextInput::make("sku"),
                        TextInput::make("price"),
                        TextInput::make("quantity"),
                        Select::make('type')->options([
                            'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                            'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                            'product' => ProductTypeEnum::PRODUCT->value,
                            'service' => ProductTypeEnum::SERVICE->value,
                        ])
                    ])->columns(2)

                ]),

                Group::make()->schema([
                    Section::make('Status')->schema([
                        Toggle::make('is_visible')->label("Visivel"),
                        Toggle::make('is_featured')->label("Apresentar"),
                        DatePicker::make('published_at')->label("Data de publicação")
                    ]),

                    Section::make('Image')->schema([
                        FileUpload::make('image')->previewable()
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
                TextColumn::make("name")->label("Nome"),
                TextColumn::make('brand.name')->label("Marca"),
                IconColumn::make('is_visible')->boolean()->label("Visivel"),
                TextColumn::make('price')->label('Preço'),
                TextColumn::make('quantity')->label("Quantidade"),
                TextColumn::make('published_at')->label("Data de publicação"),
                TextColumn::make("type")->label("Tipo")
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
