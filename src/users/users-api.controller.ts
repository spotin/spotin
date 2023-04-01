import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
} from '@nestjs/common';
import { UsersService } from './users.service';
import { CreateUserDto } from './dto/create-user.dto';
import { UpdateUserDto } from './dto/update-user.dto';
import {
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { UserDto } from './dto/user.dto';

@ApiTags('Users')
@Controller('api/users')
export class UsersApiController {
  constructor(private readonly usersService: UsersService) {}

  @Post()
  @ApiOperation({
    summary: 'Create a user.',
    description: 'Create a user.',
    operationId: 'createUserApi',
  })
  @ApiOkResponse({
    description: 'User has been successfully created.',
    type: CreateUserDto,
  })
  createUserApi(@Body() createUserDto: CreateUserDto) {
    return this.usersService.create(createUserDto);
  }

  @Get()
  @ApiOperation({
    summary: 'Get all users.',
    description: 'Get all users.',
    operationId: 'findAllUsersApi',
  })
  @ApiOkResponse({
    description: 'Users have been successfully retrieved.',
    type: [UserDto],
  })
  @ApiNotFoundResponse({
    description: 'No users found.',
  })
  findAllUsersApi() {
    return this.usersService.findAll();
  }

  @Get(':id')
  @ApiOperation({
    summary: 'Get a user.',
    description: 'Get a user.',
    operationId: 'findOneUserApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The id of the user.',
    type: String,
  })
  @ApiOkResponse({
    description: 'User has been successfully retrieved.',
    type: UserDto,
  })
  @ApiNotFoundResponse({
    description: 'User not found.',
  })
  findOneUserApi(@Param('id') id: string) {
    return this.usersService.findOne(id);
  }

  @Patch(':id')
  @ApiOperation({
    summary: 'Update a user.',
    description: 'Update a user.',
    operationId: 'updateUserApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The id of the user.',
    type: String,
  })
  @ApiOkResponse({
    description: 'User has been successfully updated.',
    type: UpdateUserDto,
  })
  @ApiNotFoundResponse({
    description: 'User not found.',
  })
  updateUserApi(@Param('id') id: string, @Body() updateUserDto: UpdateUserDto) {
    return this.usersService.update(id, updateUserDto);
  }

  @Delete(':id')
  @ApiOperation({
    summary: 'Delete a user.',
    description: 'Delete a user.',
    operationId: 'removeUserApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The id of the user.',
    type: String,
  })
  @ApiOkResponse({
    description: 'User has been successfully deleted.',
  })
  @ApiNotFoundResponse({
    description: 'User not found.',
  })
  removeUserApi(@Param('id') id: string) {
    return this.usersService.remove(id);
  }
}
