import { Controller, Body, Param } from '@nestjs/common';
import { ApiConflictResponse, ApiTags } from '@nestjs/swagger';
import { User, UserRole } from '@prisma/client';
import { UsersService } from '@/users/users.service';
import { CreateUserDto } from '@/users/dtos/create-user.dto';
import { UpdateUserDto } from '@/users/dtos/update-user.dto';
import { ReadUserDto } from '@/users/dtos/read-user.dto';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { Post } from '@/common/decorators/post.decorator';
import { Patch } from '@/common/decorators/patch.decorator';
import { Delete } from '@/common/decorators/delete.decorator';
import { RolesGuard } from '@/auth/guards/roles.guard';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { Roles } from '@/auth/decorators/roles.decorator';

@ApiTags('Users')
@Controller('api/users')
export class UsersApiController {
  constructor(private readonly usersService: UsersService) {}

  @GetMany({
    name: 'Users',
    summary: 'Get the users',
    operationId: 'getUsersApi',
    responseType: [ReadUserDto],
  })
  @JwtAuth(RolesGuard)
  @Roles(UserRole.ADMIN, UserRole.USER)
  async getUsersApi() {
    const users = await this.usersService.getUsers();

    const usersDto = users.map((user) => new ReadUserDto(user));

    return usersDto;
  }

  @GetOne({
    name: 'User',
    summary: 'Get the specified user',
    operationId: 'getSpotApi',
    responseType: ReadUserDto,
  })
  @JwtAuth(RolesGuard)
  @Roles(UserRole.ADMIN)
  async getUserApi(@Param('id') id: string) {
    const user = (await this.usersService.getUser(id)) as User;

    return new ReadUserDto(user);
  }

  @Post({
    name: 'User',
    summary: 'Create a new user',
    bodyType: CreateUserDto,
    responseType: ReadUserDto,
    operationId: 'createUserApi',
  })
  @JwtAuth(RolesGuard)
  @Roles(UserRole.ADMIN)
  @ApiConflictResponse({
    description: 'Another user has the same username.',
  })
  async createUserApi(@Body() createUserDto: CreateUserDto) {
    const newUser = await this.usersService.createUser(createUserDto);

    return new ReadUserDto(newUser);
  }

  @Patch({
    name: 'User',
    summary: 'Update the specified user',
    bodyType: UpdateUserDto,
    responseType: ReadUserDto,
    operationId: 'updateUserApi',
  })
  @JwtAuth(RolesGuard)
  @Roles(UserRole.ADMIN)
  @ApiConflictResponse({
    description: 'Another user has the same username.',
  })
  async updateUserApi(
    @Param('id') id: string,
    @Body() updateUserDto: UpdateUserDto,
  ) {
    const updatedUser = await this.usersService.updateUser(id, updateUserDto);

    return new ReadUserDto(updatedUser);
  }

  @Delete({
    name: 'User',
    summary: 'Delete the specified user',
    operationId: 'deleteUserApi',
  })
  @JwtAuth(RolesGuard)
  @Roles(UserRole.ADMIN)
  async deleteUserApi(@Param('id') id: string) {
    await this.usersService.deleteUser(id);
  }
}
