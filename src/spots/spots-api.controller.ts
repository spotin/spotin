import { Body, Controller, HttpCode, Param, Post } from '@nestjs/common';
import {
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { Prisma, User, UserRole } from '@prisma/client';
import { CreateSpotDto } from '@/spots/dtos/create-spot.dto';
import { UpdateSpotDto } from '@/spots/dtos/update-spot-type.dto';
import { SpotsService } from '@/spots/spots.service';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { CustomPatch } from '@/common/decorators/custom-patch.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { TokenOrJwtAuth } from '@/auth/token-or-jwt/token-or-jwt-auth.decorators';
import { UnconfiguredSpotAuth } from '@/auth/unconfigured-spot/unconfigured-spot-auth.decorator';

@ApiTags('API - Spots')
@Controller('api/spots')
export class SpotsApiController {
  constructor(private readonly spotsService: SpotsService) {}

  @GetMany({
    path: 'public',
    name: 'Spots',
    summary: 'Get the public spots',
    operationId: 'getPublicSpotsApi',
    responseType: [ReadSpotDto],
  })
  async getPublicSpotsApi() {
    const spots = await this.spotsService.getPublicSpots();

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @Post(':id/configure')
  @UnconfiguredSpotAuth()
  @HttpCode(200)
  @ApiOperation({
    summary: 'Configure the spot',
    description: 'Configure the spot.',
    operationId: 'configureSpotApi',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'The spot has been successfully configured.',
    type: ReadSpotDto,
  })
  async configureSpotApi(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    updateSpot.configured = true;
    updateSpot.referenced = undefined;

    const updatedSpot = await this.spotsService.updateSpot(
      id,
      {
        ...updateSpot,
        // https://www.prisma.io/docs/concepts/components/prisma-client/working-with-fields/working-with-json-fields#using-null-values
        payload: updateSpot.payload ? updateSpot.payload : Prisma.DbNull,
      },
      user,
    );

    return new ReadSpotDto(updatedSpot);
  }

  @GetMany({
    name: 'Spots',
    summary: 'Get the spots',
    operationId: 'getSpotsApi',
    responseType: [ReadSpotDto],
  })
  @TokenOrJwtAuth()
  async getSpotsApi(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @GetOne({
    name: 'Spot',
    summary: 'Get the specified spot',
    operationId: 'getSpotApi',
    responseType: ReadSpotDto,
  })
  async getSpotApi(@Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id);

    return new ReadSpotDto(spot);
  }

  @CustomPost({
    name: 'Spot',
    summary: 'Create a new spot',
    bodyType: CreateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'createSpotApi',
  })
  @TokenOrJwtAuth()
  async createSpotApi(
    @AuthUser() user: User,
    @Body() createSpotDto: CreateSpotDto,
  ) {
    const newSpot = await this.spotsService.createSpot(
      {
        ...createSpotDto,
        // https://www.prisma.io/docs/concepts/components/prisma-client/working-with-fields/working-with-json-fields#using-null-values
        payload: createSpotDto.payload ? createSpotDto.payload : Prisma.DbNull,
      },
      user,
    );

    return new ReadSpotDto(newSpot);
  }

  @CustomPatch({
    name: 'Spot',
    summary: 'Update the specified spot',
    bodyType: UpdateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'updateSpotApi',
  })
  @TokenOrJwtAuth()
  async updateSpotApi(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    if (user.role === UserRole.GUEST) {
      updateSpot.configured = true;
      updateSpot.referenced = undefined;
    }

    const updatedSpot = await this.spotsService.updateSpot(
      id,
      {
        ...updateSpot,
        // https://www.prisma.io/docs/concepts/components/prisma-client/working-with-fields/working-with-json-fields#using-null-values
        payload: updateSpot.payload ? updateSpot.payload : Prisma.DbNull,
      },
      user,
    );

    return new ReadSpotDto(updatedSpot);
  }

  @CustomDelete({
    name: 'Spot',
    summary: 'Delete the specified spot',
    operationId: 'deleteSpotApi',
  })
  @TokenOrJwtAuth()
  async deleteSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    await this.spotsService.deleteSpot(id, user);
  }
}
