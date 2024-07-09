import { Controller, Body, Param } from '@nestjs/common';
import { ApiConflictResponse, ApiTags } from '@nestjs/swagger';
import { UsersService } from '@/users/users.service';
import { CreateUserDto } from '@/users/dtos/create-user.dto';
import { UpdateUserDto } from '@/users/dtos/update-user.dto';
import { ReadUserDto } from '@/users/dtos/read-user.dto';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { CustomPatch } from '@/common/decorators/custom-patch.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { RolesGuard } from '@/auth/guards/roles.guard';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { Roles } from '@/auth/decorators/roles.decorator';
import { UserRole } from '@/users/enums/user-role';

@ApiTags('Users')
@Controller('api/users')
@JwtAuth(RolesGuard)
@Roles(UserRole.ADMIN)
export class UsersController {
	constructor(private readonly usersService: UsersService) {}

	@GetMany({
		name: 'Users',
		summary: 'Get the users',
		operationId: 'getUsers',
		responseType: [ReadUserDto],
	})
	async getUsers(): Promise<ReadUserDto[]> {
		const users = await this.usersService.getUsers();

		const usersDto = users.map((user) => new ReadUserDto(user));

		return usersDto;
	}

	@GetOne({
		name: 'User',
		summary: 'Get the specified user',
		operationId: 'getUser',
		responseType: ReadUserDto,
	})
	async getUser(@Param('id') id: string): Promise<ReadUserDto> {
		const user = (await this.usersService.getUser(id)) as User;

		return new ReadUserDto(user);
	}

	@CustomPost({
		name: 'User',
		summary: 'Create a new user',
		bodyType: CreateUserDto,
		responseType: ReadUserDto,
		operationId: 'createUser',
	})
	@ApiConflictResponse({
		description: 'Another user has the same username.',
	})
	async createUser(@Body() createUserDto: CreateUserDto): Promise<ReadUserDto> {
		const newUser = await this.usersService.createUser(createUserDto);

		return new ReadUserDto(newUser);
	}

	@CustomPatch({
		name: 'User',
		summary: 'Update the specified user',
		bodyType: UpdateUserDto,
		responseType: ReadUserDto,
		operationId: 'updateUser',
	})
	@ApiConflictResponse({
		description: 'Another user has the same username.',
	})
	async updateUser(
		@Param('id') id: string,
		@Body() updateUserDto: UpdateUserDto,
	): Promise<ReadUserDto> {
		const updatedUser = await this.usersService.updateUser(id, updateUserDto);

		return new ReadUserDto(updatedUser);
	}

	@CustomDelete({
		name: 'User',
		summary: 'Delete the specified user',
		operationId: 'deleteUser',
	})
	async deleteUser(@Param('id') id: string): Promise<void> {
		await this.usersService.deleteUser(id);
	}
}
