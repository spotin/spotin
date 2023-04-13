import { Controller, Body, Param } from '@nestjs/common';
import { UsersService } from './users.service';
import { CreateUserDto } from './dtos/create-user.dto';
import { UpdateUserDto } from './dtos/update-user.dto';
import { ApiConflictResponse, ApiTags } from '@nestjs/swagger';
import { ReadUserDto } from './dtos/read-user.dto';
import { GetMany } from 'src/common/decorators/get-many.decorator';
import { GetOne } from 'src/common/decorators/get-one.decorator';
import { Post } from 'src/common/decorators/post.decorator';
import { Patch } from 'src/common/decorators/patch.decorator';
import { Delete } from 'src/common/decorators/delete.decorator';
import { User } from '@prisma/client';

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
  async deleteUserApi(@Param('id') id: string) {
    await this.usersService.deleteUser(id);
  }
}
