import { Injectable } from '@nestjs/common';
import { PrismaService } from 'src/prisma/prisma.service';
import { CreateSpotDto } from './dtos/create-spot.dto';
import { UpdateSpotDto } from './dtos/update-spot-type.dto';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {}

  /** List spots */
  async getSpots() {
    return await this.prisma.spot.findMany();
  }

  /** Read a spot by id */
  async getSpot(spotId: string) {
    const slideshow = await this.prisma.spot.findFirst({
      where: {
        id: {
          equals: spotId,
        },
      },
    });

    return slideshow;
  }

  /** Create a new spot */
  async createSpot(createSpot: CreateSpotDto) {
    const newSpot = await this.prisma.spot.create({
      data: {
        ...createSpot,
      },
    });

    return newSpot;
  }

  /** Update a spot by id */
  async updateSpot(spotId: string, updateSpot: UpdateSpotDto) {
    const updatedSpot = await this.prisma.spot.update({
      where: {
        id: spotId,
      },
      data: {
        ...updateSpot,
      },
    });

    return updatedSpot;
  }

  /** Delete a spot by id */
  async deleteSpot(spotId: string) {
    await this.prisma.spot.delete({
      where: {
        id: spotId,
      },
    });
  }
}
