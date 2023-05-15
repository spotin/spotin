import { Injectable } from '@nestjs/common';
import { Prisma, User, UserRole } from '@prisma/client';
import { PrismaService } from 'nestjs-prisma';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {
    prisma.$use(async (params, next) => {
      // Check incoming query type
      if (params.model == 'Spot') {
        if (params.action == 'delete') {
          // Delete queries
          // Change action to an update
          params.action = 'update';
          params.args['data'] = { deleted: true };
        }
        if (params.action == 'deleteMany') {
          // Delete many queries
          params.action = 'updateMany';
          if (params.args.data != undefined) {
            // set deleted to current time
            params.args.data['deleted'] = new Date();
          } else {
            params.args['data'] = { deleted: new Date() };
          }
        }
      }
      return next(params);
    });
  }

  /** List spots */
  async getSpots(user: User) {
    return await this.prisma.spot.findMany({
      where: {
        userId: {
          equals: user.id,
        },
      },
    });
  }

  /** Read a spot by id */
  async getSpot(spotId: string) {
    const spot = await this.prisma.spot.findFirstOrThrow({
      where: {
        id: {
          equals: spotId,
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
      },
      data: {
        users: user
          ? {
              connect: {
                id: user.id,
              },
            }
          : undefined,
        ...updateSpot,
      },
    });

    return updatedSpot;
  }

  /** Delete a spot by id */
  async deleteSpot(spotId: string, user: User) {
    await this.prisma.spot.findFirstOrThrow({
      where: {
        id: spotId,
        userId: user.id,
      },
    });

    await this.prisma.spot.delete({
      where: {
        id: spotId,
      },
    });
  }

  /** List public spots */
  async getPublicSpots() {
    return await this.prisma.spot.findMany({
      where: {
        referenced: true,
      },
    });
  }
}
