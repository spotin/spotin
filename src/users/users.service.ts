import { Injectable } from '@nestjs/common';
import { PrismaService } from 'src/prisma/prisma.service';
import { Prisma } from '@prisma/client';
import * as argon2 from 'argon2';

@Injectable()
export class UsersService {
  constructor(private readonly prisma: PrismaService) {}

  async getUsers() {
    return await this.prisma.user.findMany();
  }

  async getUser(id: string) {
    return await this.prisma.user.findFirst({ where: { id } });
  }

  async getUserByUsername(username: string) {
    return await this.prisma.user.findFirst({ where: { username } });
  }

  async createUser(createUser: Prisma.UserCreateInput) {
    const newUser = await this.prisma.user.create({
      data: {
        ...createUser,
        password: await argon2.hash(createUser.password),
      },
    });
    return newUser;
  }

  async updateUser(userId: string, updateUser: Prisma.UserUpdateInput) {
    return await this.prisma.user.update({
      where: {
        id: userId,
      },
      data: updateUser,
    });
  }

  deleteUser(userId: string) {
    return this.prisma.user.delete({
      where: {
        id: userId,
      },
    });
  }
}
