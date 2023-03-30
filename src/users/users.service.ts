import { Injectable } from '@nestjs/common';
import { PrismaService } from 'src/prisma/prisma.service';
import { CreateUserDto } from './dto/create-user.dto';
import { UpdateUserDto } from './dto/update-user.dto';

@Injectable()
export class UsersService {
  constructor(private readonly prisma: PrismaService) {}
  async findOne(username: string) {
    return await this.prisma.user.findFirst({ where: { username } });
  }

  create(createUserDto: CreateUserDto) {
    const newUser = this.prisma.user.create({
      data: {
        username: createUserDto.username,
        password: createUserDto.password,
        email: createUserDto.email,
      },
    });
    return newUser;
  }

  async findAll() {
    return await this.prisma.user.findMany();
  }

  async update(userId: string, updateUserDto: UpdateUserDto) {
    return await this.prisma.user.update({
      where: {
        id: userId,
      },
      data: {
        ...updateUserDto,
      },
    });
  }

  remove(userId: string) {
    return this.prisma.user.delete({
      where: {
        id: userId,
      },
    });
  }
}
