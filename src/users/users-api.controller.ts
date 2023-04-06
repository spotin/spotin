import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  NotFoundException,
  HttpCode,
} from '@nestjs/common';
import { UsersService } from './users.service';
import { CreateUserDto } from './dtos/create-user.dto';
import { UpdateUserDto } from './dtos/update-user.dto';
import {
  ApiBody,
  ApiConflictResponse,
  ApiCreatedResponse,
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { ReadUserDto } from './dtos/read-user.dto';
import { JwtAuth } from '../auth/jwt/jwt-auth.decorator';

@ApiTags('Users')
@Controller('api/users')
export class UsersApiController {
  constructor(private readonly usersService: UsersService) {}

  @Get()
  @JwtAuth()
  @ApiOperation({
    summary: 'Get the users',
    description: 'Get the users',
    operationId: 'findAllUsersApi',
  })
  @ApiOkResponse({
    description: 'Users have been successfully retrieved.',
    type: [ReadUserDto],
  })
  async getUsersApi() {
    const users = await this.usersService.getUsers();

    const usersDto = users.map((user) => new ReadUserDto(user));

    return usersDto;
  }

  @Get(':id')
  @ApiOperation({
    summary: 'Get the specified user',
    description: 'Get the specified user.',
    operationId: 'getUserApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The user ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'User has been successfully retrieved.',
    type: ReadUserDto,
  })
  @ApiNotFoundResponse({
    description: 'User has not been found.',
  })
  async getUserApi(@Param('id') id: string) {
    const user = await this.usersService.getUser(id);

    if (!user) {
      throw new NotFoundException();
    }

    return new ReadUserDto(user);
  }

  @Post()
  @ApiOperation({
    summary: 'Create a new user',
    description: 'Create a new user.',
    operationId: 'createUserApi',
  })
  @ApiBody({
    description: "The user's details.",
    type: CreateUserDto,
  })
  @ApiCreatedResponse({
    description: 'User has been successfully created.',
    type: ReadUserDto,
  })
  @ApiConflictResponse({
    description: 'Another user has the same username.',
  })
  async createUserApi(@Body() createUserDto: CreateUserDto) {
    const newUser = await this.usersService.createUser(createUserDto);

    return new ReadUserDto(newUser);
  }

  @Patch(':id')
  @ApiOperation({
    summary: 'Update the specified user',
    description: 'Update the specified user.',
    operationId: 'updateUserApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The user ID.',
    format: 'uuid',
  })
  @ApiBody({
    description: "The user's details.",
    type: UpdateUserDto,
  })
  @ApiOkResponse({
    description: 'User has been successfully updated.',
    type: ReadUserDto,
  })
  @ApiConflictResponse({
    description: 'Another user has the same username.',
  })
  @ApiNotFoundResponse({
    description: 'User has not been found.',
  })
  async updateUserApi(
    @Param('id') id: string,
    @Body() updateUserDto: UpdateUserDto,
  ) {
    const updatedUser = await this.usersService.updateUser(id, updateUserDto);

    return new ReadUserDto(updatedUser);
  }

  @Delete(':id')
  @HttpCode(204)
  @ApiOperation({
    summary: 'Delete the specified user',
    description: 'Delete the specified user.',
    operationId: 'deleteUserApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The id of the user.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'User has been successfully deleted.',
  })
  @ApiNotFoundResponse({
    description: 'User has not been found.',
  })
  async deleteUserApi(@Param('id') id: string) {
    await this.usersService.deleteUser(id);
  }
}
