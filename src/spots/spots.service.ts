import { Injectable } from '@nestjs/common';
import { Prisma, User } from '@prisma/client';
import { PrismaService } from 'nestjs-prisma';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {}

  /** List spots */
  async getSpots(user: User) {
    return await this.prisma.spot.findMany({
      where: {
        userId: {
          equals: user.id,
        },
        deletedAt: {
          equals: null,
        },
      },
    });
  }

  /** Read a spot by id */
  async getSpot(spotId: string, user?: User) {
    const spot = await this.prisma.spot.findFirstOrThrow({
      where: {
        id: spotId,
        userId: user?.id,
        deletedAt: {
          equals: null,
        },
      },
    });

    return spot;
  }

  /** Create a new spot */
  async createSpot(createSpot: Prisma.SpotCreateWithoutUsersInput, user: User) {
    const newSpot = await this.prisma.spot.create({
      data: {
        ...createSpot,
        users: {
          connect: {
            id: user.id,
          },
        },
      },
    });

    return newSpot;
  }

  /** Update a spot by id */
  async updateSpot(
    spotId: string,
    updateSpot: Prisma.SpotUpdateInput,
    user: User | null,
  ) {
    const updatedSpot = await this.prisma.spot.update({
      where: {
        id: spotId,
        deletedAt: null,
        userId: user ? user.id : undefined,
      },
      data: {
        ...updateSpot,
      },
    });

    return updatedSpot;
  }

  /** Delete a spot by id */
  async deleteSpot(spotId: string, user: User) {
    await this.prisma.spot.update({
      where: {
        id: spotId,
        userId: user.id,
        deletedAt: null,
      },
      data: {
        deletedAt: new Date(),
      },
    });
  }

  /** List public spots */
  async getPublicSpots() {
    return await this.prisma.spot.findMany({
      where: {
        referenced: true,
        deletedAt: {
          equals: null,
        },
      },
    });
  }
}
