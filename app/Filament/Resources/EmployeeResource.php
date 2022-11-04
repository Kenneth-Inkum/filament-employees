<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\State;
use App\Models\Country;
use App\Models\Employee;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('country_id')
                            ->label('country')
                            ->options(Country::all()->pluck('name', 'id')->toArray())
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('state_id', null,)),
                            Select::make('state_id')
                            ->label('State')
                            ->options(function (callable $get){
                                $country = Country::find($get('country_id'));
                                if(!$country){
                                    return State::all()->pluck('name','id');
                                }
                                return $country->states->pluck('name','id');
                            })
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('city_id', null,)),
                            Select::make('city_id')
                            ->label('City')
                            ->options(function (callable $get){
                                $state = State::find($get('state_id'));
                                if(!$state){
                                    return State::all()->pluck('name','id');
                                }
                                return $state->city->pluck('name','id');
                            }),
                        // Select::make('country_id')
                        //     ->Relationship('country', 'name')->required(),
                        // Select::make('state_id')
                        //     ->Relationship('state', 'name')->required(),
                        // Select::make('city_id')
                        //     ->Relationship('city', 'name')->required(),
                        Select::make('department_id')
                            ->Relationship('department', 'name')->required(),
                        TextInput::make('first_name')->required(),
                        TextInput::make('last_name')->required(),
                        TextInput::make('address')->required(),
                        TextInput::make('zip_code')->required(),
                        DatePicker::make('birthday')->required(),
                        DatePicker::make('date_hired')->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                // TextColumn::make('country.name')->sortable(),
                // TextColumn::make('state.name')->sortable()->searchable(),
                // TextColumn::make('city.name')->sortable()->searchable(),
                // TextColumn::make('department.name')->sortable()->searchable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('address')->sortable()->searchable(),
                TextColumn::make('zip_code')->sortable()->searchable(),
                TextColumn::make('birthday')->sortable()->searchable(),
                TextColumn::make('date_hired')->sortable()->searchable(),
                TextColumn::make('created_at')->sortable()->searchable(),

            ])
            ->filters([
                SelectFilter::make('department')->relationship('department', 'name'),
                SelectFilter::make('city')->relationship('city', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
