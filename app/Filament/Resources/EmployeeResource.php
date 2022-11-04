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

    protected static ?string $navigationGroup = 'Employee Management';

    // protected static ?string $navigationLabel = 'Cities';

    // protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('country_id')
                            ->label('Country')
                            ->options(Country::all()->pluck('name', 'id')->toArray())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('state_id', null,)),
                            Select::make('state_id')
                            ->label('State')
                            ->required()
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
                            ->required()
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
                            ->Relationship('department', 'name')
                            ->required(),
                        TextInput::make('first_name')
                            ->required()
                            ->label('First Name')
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->label('Last Name')
                            ->maxLength(255),
                        TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('zip_code')
                            ->required()
                            ->label('Zip Code')
                            ->maxLength(5),
                        DatePicker::make('birthday')
                            ->label('Date of Birth')
                            ->minDate(now()->subYears(120))
                            ->maxDate(now())
                            ->required(),
                        DatePicker::make('date_hired')
                            ->label('Date Hired')
                            ->minDate(now()->subYears(70))
                            ->maxDate(now())
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('id')->sortable(),
                // TextColumn::make('country.name')->sortable(),
                // TextColumn::make('state.name')->sortable()->searchable(),
                TextColumn::make('first_name')->sortable()->searchable()->label('First Name'),
                TextColumn::make('last_name')->sortable()->searchable()->label('Last Name'),
                TextColumn::make('department.name')->sortable()->searchable(),
                TextColumn::make('city.name')->sortable()->searchable()->label('City'),
                TextColumn::make('address')->sortable()->searchable(),
                TextColumn::make('zip_code')->sortable()->searchable()->label('Zip Code'),
                TextColumn::make('birthday')->sortable()->searchable()->label('Date of Birth'),
                TextColumn::make('date_hired')->sortable()->searchable()->label('Date Hired'),
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
