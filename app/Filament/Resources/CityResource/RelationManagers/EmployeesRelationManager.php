<?php

namespace App\Filament\Resources\CityResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\State;
use App\Models\Country;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Form $form): Form
    {
        return $form
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
                    })
                    ->reactive(),
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
