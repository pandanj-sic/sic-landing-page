<?php

namespace App\Filament\Resources\DepartmentMembers;

use App\Filament\Resources\DepartmentMembers\Pages\CreateDepartmentMember;
use App\Filament\Resources\DepartmentMembers\Pages\EditDepartmentMember;
use App\Filament\Resources\DepartmentMembers\Pages\ListDepartmentMembers;
use App\Filament\Resources\DepartmentMembers\Schemas\DepartmentMemberForm;
use App\Filament\Resources\DepartmentMembers\Tables\DepartmentMembersTable;
use App\Models\DepartmentMember;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DepartmentMemberResource extends Resource
{
    protected static ?string $model = DepartmentMember::class;

    protected static ?string $navigationLabel = 'Department Members';

    protected static ?string $modelLabel = 'Department Member';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return DepartmentMemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentMembersTable::configure($table);
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
            'index' => ListDepartmentMembers::route('/'),
            'create' => CreateDepartmentMember::route('/create'),
            'edit' => EditDepartmentMember::route('/{record}/edit'),
        ];
    }
}
