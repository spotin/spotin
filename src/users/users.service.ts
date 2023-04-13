import { Injectable } from '@nestjs/common';
import { Prisma } from '@prisma/client';
import * as argon2 from 'argon2';
import { PrismaService } from 'nestjs-prisma';

@Injectable()
export class UsersService {
  constructor(private readonly prisma: PrismaService) {}

  async getUsers() {
    return await this.prisma.user.findMany({
      include: {
        spots: true,
      },
    });
  }

  async getUser(id: string) {
    return await this.prisma.user.findFirst({
      where: { id },
      include: {
        spots: true,
      },
    });
  }

  async getUserByUsername(username: string) {
    return await this.prisma.user.findFirst({
      where: { username },
      include: {
        spots: true,
      },
    });
  }

  async createUser(createUser: Prisma.UserCreateInput) {
    const newUser = await this.prisma.user.create({
      data: {
        ...createUser,
        password: await argon2.hash(createUser.password),
      },
      include: {
        spots: true,
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
      include: {
        spots: true,
      },
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
