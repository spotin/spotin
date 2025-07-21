import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { Roles } from '@/auth/decorators/roles.decorator';
import { RolesGuard } from '@/auth/guards/roles.guard';
import { JwtAccessTokenAuth } from '@/auth/jwt/jwt-access-token-auth.decorator';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
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
@JwtAccessTokenAuth(RolesGuard)
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
	@Render('users/form')
	async renderCreateUser(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string>> {
		const user = await this.usersService.getUser(payload.sub);

		return {
			title: 'Create a new user | Spot in',
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
	@Render('users/list')
	async renderUsersList(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string | User[]>> {
		const user = await this.usersService.getUser(payload.sub);

		const users = await this.usersService.getUsers();

		return {
			title: 'Users | Spot in',
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
	async deleteUser(@Param('id') id: string): Promise<void> {
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
	@Render('users/form')
	async renderUser(
		@AuthJwtPayload() payload: JwtPayload,
		@Param('id') id: string,
	): Promise<Record<string, string | User>> {
		const user = await this.usersService.getUser(payload.sub);

		const foundUser = await this.usersService.getUser(id);

		return {
			title: 'User | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
			user: foundUser,
		};
	}
}
