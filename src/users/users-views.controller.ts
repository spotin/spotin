import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { Roles } from '@/auth/decorators/roles.decorator';
import { RolesGuard } from '@/auth/guards/roles.guard';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { UserRole } from '@/users/enums/user-role';
import { User } from '@/users/types/user';
import { UsersService } from '@/users/users.service';
import {
	Controller,
	Get,
	HttpStatus,
	Param,
	Redirect,
	Render,
	UseFilters,
} from '@nestjs/common';
import {
	ApiOkResponse,
	ApiOperation,
	ApiParam,
	ApiTags,
} from '@nestjs/swagger';

@ApiTags('Views')
@Controller('users')
@JwtAuth(RolesGuard)
@Roles(UserRole.ADMIN)
@UseFilters(UnauthorizedViewExceptionFilter)
export class UsersViewsController {
	constructor(private readonly usersService: UsersService) {}

	@Get('create')
	@ApiOperation({
		summary: 'Render the create a new user page',
		description: 'Render the create a new user page.',
		operationId: 'renderCreateUser',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('users/create')
	renderCreateUser(@AuthUser() user: User): Record<string, string> {
		return {
			title: 'ui.users.create.title',
			description: 'ui.users.create.description',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get()
	@ApiOperation({
		summary: 'Render the users page',
		description: 'Render the users page.',
		operationId: 'renderUsersList',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('users/index')
	async renderUsersList(
		@AuthUser() user: User,
	): Promise<Record<string, string | User[]>> {
		const users = await this.usersService.getUsers();

		return {
			title: 'ui.users.index.title',
			description: 'ui.users.index.description',
			username: user.username,
			email: user.email,
			role: user.role,
			users,
		};
	}

	@Get(':id/delete')
	@ApiOperation({
		summary: 'Delete the specified user',
		description: 'Delete the specified user. Redirect to `/users`.',
		operationId: 'deleteUser',
	})
	@ApiParam({
		name: 'id',
		description: 'The user ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Redirect successful.',
	})
	@Redirect('/users', HttpStatus.PERMANENT_REDIRECT)
	async deleteUser(
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<void> {
		await this.usersService.deleteUser(id);
	}

	@Get(':id')
	@ApiOperation({
		summary: 'Render the user page',
		description: 'Render the user page.',
		operationId: 'renderUser',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@ApiParam({
		name: 'id',
		description: 'The user ID.',
		format: 'uuid',
	})
	@Render('users/edit')
	async renderUser(
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<Record<string, string | User>> {
		const foundUser = await this.usersService.getUser(id);

		return {
			title: 'ui.users.edit.title',
			description: 'ui.users.edit.description',
			username: user.username,
			email: user.email,
			role: user.role,
			user: foundUser,
		};
	}
}
